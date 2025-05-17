import 'package:flutter/material.dart';
import '../services/local_storage_service.dart';
import 'package:cloud_firestore/cloud_firestore.dart';

class BookingPage extends StatefulWidget {
  final Map<String, dynamic> car;

  const BookingPage({super.key, required this.car});

  @override
  _BookingPageState createState() => _BookingPageState();
}

class _BookingPageState extends State<BookingPage> {
  final userID = LocalStorageService().readData('uid');
  String? selectedCity;
  DateTime? startDate;
  DateTime? endDate;
  int totalDays = 0;
  late double pricePerDay;

  @override
  void initState() {
    super.initState();
    pricePerDay = double.parse(widget.car['price']!);
  }

  double calculateTotalPrice() {
    return totalDays * pricePerDay;
  }

  Future<void> _pickDate(BuildContext context, bool isStartDate) async {
    DateTime initialDate = DateTime.now();
    DateTime firstDate = DateTime(2000);
    DateTime lastDate = DateTime(2099);

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: firstDate,
      lastDate: lastDate,
    );

    if (picked != null) {
      setState(() {
        if (isStartDate) {
          startDate = picked;
        } else {
          endDate = picked;
        }

        if (startDate != null && endDate != null) {
          totalDays = endDate!.difference(startDate!).inDays + 1;
        }
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        centerTitle: true,
        title: const Text(
          'CityDrive',
          style: TextStyle(
            fontFamily: 'Marcellus',
            fontWeight: FontWeight.w500,
          ),
        ),
        backgroundColor: const Color.fromARGB(255, 140, 140, 140),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                height: 250,
                decoration: BoxDecoration(
                  color: const Color.fromARGB(255, 140, 140, 140),
                  borderRadius: BorderRadius.circular(10),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 10,
                      offset: const Offset(0, 5),
                    ),
                  ],
                  image: DecorationImage(
                    image: NetworkImage(widget.car['image']!),
                  ),
                ),
              ),
              const SizedBox(height: 10),
              Text(
                widget.car['make']!+' '+widget.car['model']!,
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 20),
              // Date Inputs
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Start Date'),
                        const SizedBox(height: 5),
                        GestureDetector(
                          onTap: () => _pickDate(context, true),
                          child: Container(
                            height: 50,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: Colors.grey),
                            ),
                            child: Center(
                              child: Text(
                                startDate == null
                                    ? 'Select Start Date'
                                    : '${startDate!.toLocal()}'.split(' ')[0],
                                style: const TextStyle(fontSize: 16),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('End Date'),
                        const SizedBox(height: 5),
                        GestureDetector(
                          onTap: () => _pickDate(context, false),
                          child: Container(
                            height: 50,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: Colors.grey),
                            ),
                            child: Center(
                              child: Text(
                                endDate == null
                                    ? 'Select End Date'
                                    : '${endDate!.toLocal()}'.split(' ')[0],
                                style: const TextStyle(fontSize: 16),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('City'),
                  const SizedBox(height: 5),
                  DropdownButtonFormField<String>(
                    value: selectedCity,
                    items: const [
                      DropdownMenuItem(
                        value: 'Erbil',
                        child: Text('Erbil'),
                      ),
                      DropdownMenuItem(
                        value: 'Sulaymaniyah',
                        child: Text('Sulaymaniyah'),
                      ),
                      DropdownMenuItem(
                        value: 'Duhok',
                        child: Text('Duhok'),
                      ),
                    ],
                    onChanged: (value) {
                      setState(() {
                        selectedCity = value;
                      });
                    },
                    decoration: InputDecoration(
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 10),
                    ),
                  ),

                ],
              ),
              Column(
                children: [
                  _buildPriceRow('Price per day:', '${widget.car['price']}\$'),
                  _buildPriceRow('Total price:', '${calculateTotalPrice()}\$'),
                ],
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () async {
                  if (startDate == null || endDate == null) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                          content:
                              Text('Please select both start and end dates.')),
                    );
                    return;
                  }

                  try {
                    await FirebaseFirestore.instance.collection('bookings').add({
                      'userID': userID,
                      'carID': widget.car['id'],
                      'startDate': Timestamp.fromDate(startDate!),
                      'endDate': Timestamp.fromDate(endDate!),
                      'totalPrice': calculateTotalPrice(),
                      'status': 'pending',
                      'createdAt': Timestamp.now(),
                      'city': selectedCity,
                    });

                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                          content: Text('Booking submitted successfully!')),
                    );

                    Navigator.pop(context); // or navigate to another screen
                  } catch (e) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Error: ${e.toString()}')),
                    );
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color.fromARGB(255, 140, 140, 140),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(30),
                  ),
                  padding:
                      const EdgeInsets.symmetric(horizontal: 50, vertical: 10),
                ),
                child: const Text(
                  'Submit',
                  style: TextStyle(fontSize: 20, color: Colors.white),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPriceRow(String title, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w500),
          ),
          Text(
            value,
            style: const TextStyle(fontSize: 16),
          ),
        ],
      ),
    );
  }
}
