import 'package:citydrive/pages/booking.dart';
import 'package:flutter/material.dart';

class CarDetailsPage extends StatelessWidget {
  final Map<String, dynamic> car;

  const CarDetailsPage({super.key, required this.car});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        centerTitle: true,
        title: const Text(
          'CityDrive',
          style: TextStyle(fontFamily: 'Marcellus'),
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
                  color: const Color.fromARGB(255, 140, 140, 140), // Background color
                  borderRadius: BorderRadius.circular(10), // Rounded corners
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.1),
                        blurRadius: 10,
                        offset: const Offset(0, 5),
                      ),
                    ],
                  image: DecorationImage(
                    image: NetworkImage(car['image']!),
                  ),
                ),
              ),
              const SizedBox(height: 10),


              Text(
                car['make']! +' '+ car['model']! ,
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),

              // Car Details
              Container(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    _buildCarDetailRow('Make', car['make']!),
                    const Divider(
                      thickness: 3,
                      color: Colors.grey,
                    ),
                    _buildCarDetailRow('Model', car['model']!),
                    const Divider(
                      thickness: 3,
                      color: Colors.grey,
                    ),
                    _buildCarDetailRow('Year', car['year']!.toString()),
                    const Divider(
                      thickness: 3,
                      color: Colors.grey,
                    ),
                    _buildCarDetailRow('Seater', car['seater']!.toString()),
                    const Divider(
                      thickness: 3,
                      color: Colors.grey,
                    ),
                    _buildCarDetailRow('Price per day', car['price']!.toString()),
                    const Divider(
                      thickness: 3,
                      color: Colors.grey,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Rent Now Button
              ElevatedButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => BookingPage(car: car),
                    ),
                  );
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
                  'Rent Now',
                  style: TextStyle(fontSize: 20, color: Colors.white),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCarDetailRow(String title, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w500,
            ),
            softWrap: true,
            overflow: TextOverflow.visible,
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 16,
            ),
          ),
        ],
      ),
    );
  }
}
