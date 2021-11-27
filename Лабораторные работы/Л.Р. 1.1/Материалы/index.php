<!doctype html>
<html lang="ru">
<head>
   <meta charset="utf-8" />
   <title>Расчет свойст веществ</title>
   <script src="assets/js/jquery-2.2.4.min.js"></script>
   <!-- (Problems with CORS) script src="http:www.coolprop.sourceforge.net/jscript/coolprop.js"></script -->
   <script src="coolprop.js"></script>
</head>
<body>
   <div class="ui-widget">
      <label>Вещество: </label>
      <select id="FluidName">
         <option selected>Nitrogen</option>
         <option>Helium</option>
         <option>Neon</option>
         <option>Hydrogen</option>
         <option>Argon</option>
         <option>Oxygen</option>
         <option>R134a</option>
      </select>
   </div>
   <div class="ui-widget">
      <label>Параметр 1</label>
      <select id="Name1">
         <option value="">Выберите...</option>
         <option value="Pressure" selected>Давление [Па]</option>
         <option value="Temperature">Температура [K]</option>
         <option value="Density">Плотность [кг/мм3]</option>
      </select>
      <input id='Value1' value="101325"></input>
   </div>
   <div class="ui-widget">
      <label>Параметр 2</label>
      <select id="Name2">
         <option value="">Выберите...</option>
         <option value="Pressure">Давление [Па]</option>
         <option value="Temperature" selected>Температура [K]</option>
         <option value="Density">Плотность [кг/мм3]</option>
      </select>
      <input id='Value2' value="300"></input>
   </div>
   <button id="calc">Расчет свойств</button>
   <div class="ui-widget">
      <label>Результат: </label>
   </div>
   <div class="ui-widget">
      <p id="output">
      </div>
      <script>
         function text2key(text) {
            if(text == 'Давление [Па]') return 'P';
            else if(text == 'Температура [K]') return 'T';
            else if(text == 'Плотность [кг/мм3]') return 'D';
         }
         $('#calc').click(function() {
            var name = $('#FluidName :selected').text()
            var key1 = text2key($('#Name1 :selected').text())
            var key2 = text2key($('#Name2 :selected').text())
            var val1 = parseFloat($('#Value1').val())
            var val2 = parseFloat($('#Value2').val())
            console.log(val2)
            var T = Module.PropsSI('T', key1, val1, key2, val2, name)
            var rho = Module.PropsSI('D', key1, val1, key2, val2, name)
            var p = Module.PropsSI('P', key1, val1, key2, val2, name)
            var s = Module.PropsSI('S', key1, val1, key2, val2, name)
            var h = Module.PropsSI('H', key1, val1, key2, val2, name)
            var cp = Module.PropsSI('C', key1, val1, key2, val2, name)
            var sL = Module.PropsSI('T', 'P', 101325, 'Q', 1, name)
            var sG = Module.PropsSI('T', 'P', 101325, 'Q', 0, name)
            text = ''
            text += 'T = ' + T.toFixed(2) + ' K\n' + '<br>'
            text += 'rho = ' + rho.toFixed(2) + ' кг/м<sup>3</sup>; <br>'
            text += 'p = ' + p.toFixed(2) + ' Па = ' + (p / 100000).toFixed(2) + ' бар;<br>'
            text += 's = ' + (s / 1000).toFixed(2) + ' кДж/кг/K;<br>'
            text += 'h = ' + (h / 1000).toFixed(2) + ' кДж/кг;<br>'
            text += 'cp = ' + (cp / 1000).toFixed(2) + ' кДж/кг/K;<br>'
            text += 'Нормальная температура конденсации = ' + sG.toFixed(2) + ' K = ' + (sG - 273.1415).toFixed(2) + ' °C;<br>'
            text += 'Нормальная температура кипения = ' + sL.toFixed(2) + ' K = ' + (sG - 273.1415).toFixed(2) + ' °C<br>'
            $("#output").html(text);
         });
      </script>
   </body>
   </html>