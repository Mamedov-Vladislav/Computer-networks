<?php

error_reporting (E_ALL);
ini_set ('display_errors', true);

// подключение библиотеки CoolProp
require "CoolProp.php";

// получение данных из GET запроса
$operation = $_GET['operation'] ?? '';
$value = $_GET['value'] ?? '';
$target = $_GET['target'] ?? '';
$id = $_GET['id'] ?? '';


if(
	$operation === ''
	OR $value === ''
	OR $target === ''
	OR $id === '')
{
	echo "Некорректный запрос";
} else {
	// упрощенная логика обработки запроса
	if($operation === 'get'){
		if($value === 'expander-efficiency'){
			if($target === 'expander'){
				if($id === '1'){

					$request = array(
						"operation" =>  $operation,
						"value" => $value,
						"target" => $target,
						"id" => $id
					);
					$requestJSON = json_encode($request);

					// получение данных с микроконтроллера блока ожижения
					$getResponse = file_get_contents('http://192.168.1.251/?'.$requestJSON."!");
					$response = json_decode($getResponse, JSON_UNESCAPED_UNICODE);
					

					// // температура в точке 2
					$T2 = $response["T2"];

					// // температура в точке 4
					$T4 = $response["T4"];

					// // давление в точке 2
					$p2 = $response["p2"];

					// // давление в точке 4
					$p4 = $response["p4"];

					// $T2 = 75;
					// $p2 = 100;

					// $T4 = 36;
					// $p4 = 1.3;


					// расчет термодинамических параметров
					$h2 = round(PropsSI("H", "T", $T2, "P", $p2 * 100000, "HYDROGEN") / 1000, 2);

					$h4 = round(PropsSI("H", "T", $T4, "P", $p4 * 100000, "HYDROGEN") / 1000, 2);

					$s2 = round(PropsSI("S", "T", $T2, "P", $p2 * 100000, "HYDROGEN"), 2);

					$s2s = $s2;

					$h2s = round(PropsSI("H", "S", $s2s, "P", $p4 * 100000, "HYDROGEN") / 1000, 2);

					$eff = round(($h2 - $h4)/($h2 - $h2s) * 100, 2);
					echo("Температура на входе: $T2 K<br>");
					echo("Давление на входе: $p2 бар<br>");
					echo("Температура на выходе: $T4 K<br>");
					echo("Давление на выходе: $p4 бар<br>");
					echo("Изоэнтропный КПД детандера: $id: η<sub>s</sub>=(h2 - h4)/(h2 - h2s)=($h2 - $h4)/($h2 - $h2s) = $eff %");
					die;
				}
			}
		}
	}
	echo "Некорректный запрос";
}