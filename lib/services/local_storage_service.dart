import 'package:get_storage/get_storage.dart';

class LocalStorageService{
  final box = GetStorage();

  void saveData(String key, dynamic value){
    box.write(key, value);
  }

  dynamic readData(String key){
    return box.read(key);
  }


  void removeData(String key){
    box.remove(key);
  }
}