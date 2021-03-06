#include <SPI.h>
#include <Ethernet.h>
#include <ArduinoJson.h>
#include <Regexp.h>

//Сервер
byte mac[] = {
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED // DE:AD:BE:EF:FE:ED
};

EthernetServer server(80);

void setup() {
  //Ожидаение прослушивания com-порта
  Serial.begin(9600);
  while (!Serial) {
    ;
  }

  //Инициализация сервера
  Ethernet.begin(mac);
  server.begin();
  Serial.print("Server start at ");
  Serial.println(Ethernet.localIP());

}

void loop() {

  EthernetClient client = server.available();
  if (client) {
    // ---
    // Выделение памяти для получения Get параметров
    // ---
    boolean getReqFirst = true;
    boolean getReqStart = false;
    boolean getReqEnd = false;
    String getData = "";
    boolean currentLineIsBlank = true;
    // ---
    // Выделение памяти для работы с JSON и регулярными выражениями
    // ---
    StaticJsonDocument<200> data;
    MatchState ms;
    Serial.println(">>> Новый запрос");
    // ---
    // Получение Get запроса
    // ---
    while (client.connected()) {
      if (client.available()) {

        // текущий принятый символ запроса
        char c = client.read();

        // останавливается прием get параметров
        // если встретился "!"
        if (c == '!' && getReqFirst) {
          getReqFirst = false;
          getReqEnd = true;
          Serial.println(">>> Get end");
        }

        // идет прием get параметров
        // если идет первый get запрос
        if (getReqStart && getReqFirst) {
          getData += c;
        }

        // запускается прием get параметров
        // если это первая подстрока, начинающаяся с "?"
        if (c == '?' && getReqFirst) {
          getReqStart = true;
          Serial.println(">>> Get start");
        }

        if (c == '\n' && currentLineIsBlank) {
          if (getReqEnd) {
            client.println("HTTP/1.1 200 OK");
            client.println("Content-Type: application/json");
            client.println("Connection: close");
            client.println();

            StaticJsonDocument<300> doc;


            // Подготовка JSON
            getData.replace("%22","\"");
            DeserializationError error = deserializeJson(doc, getData.c_str());

            // Проверка JSON на наличие ошибок
            if (error) {
              Serial.print(F("Ошибка инициализации JSON: "));
              Serial.println(error.f_str());
              return;
            }
            const char* operation = doc["operation"];
            Serial.print("Сервер отправил команду: ");
            Serial.println(operation);
            
            const char* value = doc["value"];
            Serial.print("Объект обработки: ");
            Serial.println(value);
            
            const char* target = doc["target"];
            Serial.print("Элемент цикла: ");
            Serial.println(target);
            
            const char* id = doc["id"];
            Serial.print("Id элементв цикла: ");
            Serial.println(id);              

            // Ответ
            data["status"] = "OK";
            data["T2"] = random(65, 72);
            data["T4"] = String(random(21, 23)) + "." +  String(random(4, 9));
            data["p2"] = random(100, 105);
            data["p4"] = "1."+ String(random(20, 35));


            serializeJsonPretty(data, client);
                        
          } else {
            client.println("HTTP/1.1 200 OK");
            client.println("Content-Type: application/json");
            client.println("Connection: close");
            client.println();

            // Ответ
            data["status"] = "ERROR";
            data["eror"] = "Некорректный запрос";
            serializeJsonPretty(data, client);
          }
          break;
        }

        if (c == '\n')
        {
          currentLineIsBlank = true;
        }
        else if (c != '\r')
        {
          currentLineIsBlank = false;
        }
      }
    }
  }

  delay(1);
  client.stop();
}
