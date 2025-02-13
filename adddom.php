<?php
include ("inc/head.php");
AutorizeProtect();
global $connect;
global $usr;
echo '<div class="contadiner">';
			// Получение адреса из GET-параметров и очистка от лишних пробелов и специальных символов
			$adress = trim(h($_GET['adress'], ENT_QUOTES,  "utf-8"));
			// Получение данных о выходах из GET-параметров и очистка от лишних пробелов и специальных символов
			$vihod1 = h($_GET['vihod']['0'], ENT_QUOTES,  "utf-8");
			$vihod2 = h($_GET['vihod']['1'], ENT_QUOTES,  "utf-8");
			$vihod3 = h($_GET['vihod']['2'], ENT_QUOTES,  "utf-8");
			$vihod4 = h($_GET['vihod']['3'], ENT_QUOTES,  "utf-8");
			$vihod5 = h($_GET['vihod']['4'], ENT_QUOTES,  "utf-8");
			$vihod = h($_GET['vihod'], ENT_QUOTES,  "utf-8");
			// Получение дополнительной информации из GET-параметров и очистка от лишних пробелов и специальных символов
			$dopzamok = h($_GET['dopzamok'], ENT_QUOTES,  "utf-8");
			$oboryda = h($_GET['oboryda'], ENT_QUOTES,  "utf-8");
			$kluch = h($_GET['klych'], ENT_QUOTES,  "utf-8");
			$pred = h($_GET['pred'], ENT_QUOTES,  "utf-8");
			$phone = h($_GET['phone'], ENT_QUOTES,  "utf-8");
			$krisha = h($_GET['krisha'], ENT_QUOTES,  "utf-8");
			$lesnica = h($_GET['lesnica'], ENT_QUOTES,  "utf-8");
			$pon = h($_GET['pon'], ENT_QUOTES,  "utf-8");
			$region = h($_GET['region'], ENT_QUOTES,  "utf-8");
			$podjezd = h($_GET['podjezd'], ENT_QUOTES,  "utf-8");
			$prim = h(trim($_GET['text']), ENT_QUOTES,  "utf-8");
			$new = h($_GET['new'], ENT_QUOTES,  "utf-8");
			// Проверка, является ли дом новым
			if($new == 1){
				$news = "1";
			}else{
				$news = "0";
			}
			// Проверка, введен ли адрес
			if (empty($adress)) {
				echo 'Введите адрес дома';
				exit;
			}
			// Проверка, указан ли председатель
			if (empty($pred)) {
				$pred = "Не указан председатель";
			}
			// Проверка, указан ли ключ
			if (empty($kluch)) {
				$kluch = "В какой кв. ключ?";
			}
			// Проверка, есть ли уже такой адрес в базе данных
			$results = $connect->query("SELECT * FROM navigard_adress WHERE adress = '$adress'");
			if ($results->num_rows == 1) {
				echo '<div class="alert alert-danger" role="alert">
							Адрес уже есть в базе
				</div>';
				redirect("result.php?adress=$adress");
				exit;
			}
			// Получение имени пользователя из массива $usr
			$user = $usr['name'];
			// Получение текущей даты и времени в формате "день.месяц.год часы:минуты:секунды"
			$date = date("d.m.Y H:i:s");
			// Проверка, есть ли примечание
			if(empty($prim)){
				// Если дом новый, то добавляем соответствующую информацию в примечание
				if($new == 0){
					$prim = $log = "Пользователь $user добавил дом $adress";
				}else{
					$prim = $log = "Пользователь $user добавил шаблон дома $adress";
				}
			    $zap = "INSERT INTO navigard_log (kogda, log)
			            VALUES (
			            '$date',
			            '$log'
			            )";
			}
			// Если есть текст в GET-параметрах, то очищаем его от лишних пробелов и специальных символов и сохраняем в переменную $prim
			if(!empty($_GET['text'])){
				$prim =  h($_GET['text'], ENT_QUOTES,  "utf-8");
			}
			// Добавление информации о доме в базу данных
			$sql = "INSERT INTO navigard_adress (adress, vihod, vihod2, vihod3, vihod4, vihod5, oboryda, dopzamok, kluch, pred, phone, krisha, lesnica, pon, podjezd, text, editor, region, new)
			VALUES (
			'$adress',
			'$vihod1',
			'$vihod2',
			'$vihod3',
			'$vihod4',
			'$vihod5',
			'$oboryda',
			'$dopzamok',
			'$kluch',
			'$pred',
			'$phone',
			'$krisha',
			'$lesnica',
			'$pon',
			'$podjezd',
			'$prim',
			'$user',
			'$region',
			'$news'
			)";
			// Если запрос выполнен успешно, то перенаправляем пользователя на страницу со всеми домами
			if ($connect->query($sql) === true) {
				redirect('all.php');
			} else {
				// Если запрос не выполнен, то выводим ошибку
				echo $connect->error;
			}
	?>
</div>
<?php
include ('inc/foot.php');

