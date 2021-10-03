<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8" />
	<title>Расчет свойст веществ</title>
	<script src="assets/js/jquery-2.2.4.min.js"></script>
	<!-- (Problems with CORS) script src="http://www.coolprop.sourceforge.net/jscript/coolprop.js"></script -->
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
		<input id ='Value1' value="101325"></input>
	</div>
	<div class="ui-widget">
		<label>Параметр 2</label>
		<select id="Name2">
			<option value="">Выберите...</option>
			<option value="Pressure">Давление [Па]</option>
			<option value="Temperature" selected>Температура [K]</option>
			<option value="Density">Плотность [кг/мм3]</option>
		</select>
		<input id ='Value2' value="300"></input>
	</div>

	<button id="calc">Рассчитать</button>
	<button id="calcFreeg">Фреонка</button>
	<div  class="ui-widget"> 
		<label>Результат: </label>
	</div>
	<div class="ui-widget">
		<p id="output">
		</div>

		<script>
			function text2key(text)
			{
				if (text == 'Давление [Па]')
					return 'P';
				else if (text == 'Температура [K]')
					return 'T';
				else if (text == 'Плотность [кг/мм3]')
					return 'D';
			}
			function freegCalc(){
				let freon = 'R134a';

				let Tboil = 263;
				let Tcond = 300;

				class TPoint {
					constructor(name){
						this.name = name;
					}

					setT(val, mesuar){
						this._T = new Temperature(val, mesuar);
					}

					getT(){
						if (this._T){
							return this._T;
						}
						else
						{
							console.log('Температура в точке не задана!');
						}						
					}

					set p(p){
						this._p = p;
					}
					
					get p(){
						if (this._p){
							return this._p;
						}
						else
						{
							console.log('Давление в точке ' + this.name + ' не задано!');
							return 1;
						}						
					}


				}

				class Temperature {
					
					constructor(val, mesuar){
						this.val = val;
						this.mesuar = mesuar;
					}
					
					toK(){
						if(this.mesuar == 'K'){
							return this.val;
						} else if(this.mesuar == 'C'){
							return this.val + 293.15;
						}
					}

					toC(){
						if(this.mesuar == 'K'){
							return this.val - 293.15;
						} else if(this.mesuar == 'C'){
							return this.val;
						}
					}

					val(){
						return this.val;
					}
				}
				/*Параметры насыщенного пара*/
				let P1 = new TPoint('1');
				P1.setT(Tboil, 'K');


				// console.log(P1.getT().toK());


				let T1 = Tboil;

				let p1 = Module.PropsSI('P', 'T', Tboil, 'Q', 1, freon)/100000;

				let p2 = Module.PropsSI('P', 'T', Tcond, 'Q', 1, freon)/100000;
				
				let s1 = Module.PropsSI('S', 'T', Tboil, 'Q', 1, freon)/1000;

				let h1 = Module.PropsSI('H', 'T', Tboil, 'Q', 1, freon)/1000;

				let s2s = s1;

				let T2s = Module.PropsSI('T', 'S', s2s*1000, 'P', p2*100000, freon);

				let h2s = Module.PropsSI('H', 'S', s2s*1000, 'P', p2*100000, freon)/1000;


				let Lcomp = h2s - h1;

				let EtaComp = 0.75;	

				let LcompReal = Lcomp/EtaComp;

				let h2 = h1 + LcompReal;

				let T2 = Module.PropsSI('T', 'H', h2*1000, 'P', p2*100000, freon);

				let s2 = Module.PropsSI('S', 'H', h2*1000, 'P', p2*100000, freon)/1000;



				let T3 = Tcond;

				let p3 = p2;

				let h3 = Module.PropsSI('H', 'T', T3, 'Q', 0, freon)/1000;

				let s3 = Module.PropsSI('S', 'T', T3, 'Q', 0, freon)/1000;


				let p4 = p1;
				let T4 = Tboil;
				let h4 = h3;
				let s4 = Module.PropsSI('S', 'H', h3*1000, 'P', p4*100000, freon)/1000;


				let qx = h1 - h4;

				let eps = qx/LcompReal;

				let epsCarno = (Tboil/(Tcond - Tboil));

				let sts = eps/epsCarno;


				console.log('Точка 1');
				console.log('p= ' + p1.toFixed(2), ' бар');
				console.log('T= ' + T1.toFixed(2), ' K');
				console.log('h= ' + h1.toFixed(2), ' кДж/кг');
				console.log('s= ' + s1.toFixed(2), ' кДж/кг/K');
				
				console.log('Точка 2s');
				console.log('p= ' + p2.toFixed(2), ' бар');
				console.log('T= ' + T2s.toFixed(2), ' K');
				console.log('h= ' + h2s.toFixed(2), ' кДж/кг');
				console.log('s= ' + s2s.toFixed(2), ' кДж/кг/K');


				console.log('Точка 2');
				console.log('p= ' + p2.toFixed(2), ' бар');
				console.log('T= ' + T2.toFixed(2), ' K');
				console.log('h= ' + h2.toFixed(2), ' кДж/кг');
				console.log('s= ' + s2.toFixed(2), ' кДж/кг/K');

				console.log('Точка 3');
				console.log('p= ' + p3.toFixed(2), ' бар');
				console.log('T= ' + T3.toFixed(2), ' K');
				console.log('h= ' + h3.toFixed(2), ' кДж/кг');
				console.log('s= ' + s3.toFixed(2), ' кДж/кг/K');


				console.log('Точка 4');
				console.log('p= ' + p4.toFixed(2), ' бар');
				console.log('T= ' + T4.toFixed(2), ' K');
				console.log('h= ' + h4.toFixed(2), ' кДж/кг');
				console.log('s= ' + s4.toFixed(2), ' кДж/кг/K');

				console.log('----------------------');
				console.log('Изоэнтропная работа сжатия - ' + Lcomp.toFixed(2) + ' кДж/кг');
				console.log('Действительная работа сжатия - ' + LcompReal.toFixed(2) + ' кДж/кг');
				console.log('Холодопроизводительность - ' + qx.toFixed(2) + ' кДж/кг');
				console.log('Холодильный коэффициент - ' + eps.toFixed(2));
				console.log('ХК Карно - ' + epsCarno.toFixed(2));
				console.log('СТС - ' + sts.toFixed(2));
				console.log('----------------------');



				let text = '';

				text += '--------Точка 1--------' +';<br>';
				text += 'p= ' + p1.toFixed(2) + ' бар' +';<br>';
				text += 'T= ' + T1.toFixed(2) + ' K' +';<br>';
				text += 'h= ' + h1.toFixed(2) + ' кДж/кг' +';<br>';
				text += 's= ' + s1.toFixed(2) + ' кДж/кг/K' +';<br>';
				
				text += '--------Точка 2s--------' +';<br>';
				text += 'p= ' + p2.toFixed(2) + ' бар' +';<br>';
				text += 'T= ' + T2s.toFixed(2) + ' K' +';<br>';
				text += 'h= ' + h2s.toFixed(2) + ' кДж/кг' +';<br>';
				text += 's= ' + s2s.toFixed(2) + ' кДж/кг/K' +';<br>';


				text += '--------Точка 2--------' +';<br>';
				text += 'p= ' + p2.toFixed(2) + ' бар' +';<br>';
				text += 'T= ' + T2.toFixed(2) + ' K' +';<br>';
				text += 'h= ' + h2.toFixed(2) + ' кДж/кг' +';<br>';
				text += 's= ' + s2.toFixed(2) + ' кДж/кг/K' +';<br>';

				text += '--------Точка 3--------' +';<br>';
				text += 'p= ' + p3.toFixed(2) + ' бар' +';<br>';
				text += 'T= ' + T3.toFixed(2) + ' K' +';<br>';
				text += 'h= ' + h3.toFixed(2) + ' кДж/кг' +';<br>';
				text += 's= ' + s3.toFixed(2) + ' кДж/кг/K' +';<br>';


				text += '--------Точка 4--------' +';<br>';
				text += 'p= ' + p4.toFixed(2) + ' бар' +';<br>';
				text += 'T= ' + T4.toFixed(2) + ' K' +';<br>';
				text += 'h= ' + h4.toFixed(2) + ' кДж/кг' +';<br>' ;
				text += 's= ' + s4.toFixed(2) + ' кДж/кг/K'+';<br>';

				text += '----------------------'+';<br>';
				text += 'Изоэнтропная работа сжатия - ' + Lcomp.toFixed(2) + ' кДж/кг'+';<br>';
				text += 'Действительная работа сжатия - ' + LcompReal.toFixed(2) + ' кДж/кг'+';<br>';
				text += 'Холодопроизводительность - ' + qx.toFixed(2) + ' кДж/кг'+';<br>';
				text += 'Холодильный коэффициент - ' + eps.toFixed(2)+';<br>';
				text += 'ХК Карно - ' + epsCarno.toFixed(2)+';<br>';
				text += 'СТС - ' + sts.toFixed(2)+';<br>';
				return text;

			}

			$('#calcFreeg').click( function() {
				//freegCalc();
				$( "#output" ).html(freegCalc());
			});


			
    //using jQuery
    $('#calc').click( function() {
    	var name = $('#FluidName :selected').text()
    	var key1 = text2key($('#Name1 :selected').text())
    	var key2 = text2key($('#Name2 :selected').text())
    	var val1 = parseFloat($('#Value1').val())
    	var val2 = parseFloat($('#Value2').val())

    	// console.log(val2)

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
    	text += 'p = ' + p.toFixed(2) + ' Па = ' + (p/100000).toFixed(2) + ' бар;<br>'
    	text += 's = ' + (s/1000).toFixed(2) + ' кДж/кг/K;<br>'
    	text += 'h = ' + (h/1000).toFixed(2) + ' кДж/кг;<br>'
    	text += 'cp = ' + (cp/1000).toFixed(2) + ' кДж/кг/K;<br>'
    	text += 'Нормальная температура конденсации = ' + sG.toFixed(2) + ' K = ' + (sG - 273.1415).toFixed(2) +' °C;<br>'
    	text += 'Нормальная температура кипения = ' + sL.toFixed(2) + ' K = ' + (sG - 273.1415).toFixed(2) +' °C<br>'

    	$( "#output" ).html( text);





    });
</script>

</body>

</html>
