import 'package:firebase_auth/firebase_auth.dart';
import 'package:flutter/material.dart';
import 'pages/login.dart';
import 'pages/register.dart';
import 'pages/cars.dart';
import 'package:get_storage/get_storage.dart';
import 'package:firebase_core/firebase_core.dart';


void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  await GetStorage.init();
  runApp(const CityDriveApp());
}

class CityDriveApp extends StatefulWidget {
  const CityDriveApp({super.key});

  @override
  State<CityDriveApp> createState() => _MyAppState();
}

class _MyAppState extends State<CityDriveApp>{

  @override
  void initState() {
    FirebaseAuth.instance.authStateChanges()
        .listen((User? user) {
      if (user == null) {
        print('++++++++User is currently signed out!');
      } else {
        print('++++++++++++User is signed in!');
      }
    });
    super.initState();
  }

  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      initialRoute: '/login',
      routes: {
        '/login': (context) => LoginPage(),
        '/register': (context) => RegisterPage(),
        '/cars': (context) => CarsPage(),
      },
    );
  }
}




