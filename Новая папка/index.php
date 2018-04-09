<html>
	<head>
		<title>Регистрация пользователя</title>
		<script  src="http://code.jquery.com/jquery-3.3.1.min.js"
			  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			  crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>
<?php
	require 'vendor/autoload.php';  //подключаем все что использовано через композер
			
			$dbParams=require('db.php');  // вызываем файл db.php
				$db= new PDO(
					$dbParams['connection'],
					$dbParams['username'],
					$dbParams['password']
				); // установка подключения к базе данных
		if(count($_POST)>0){
			//усли уже заполнены данные формы 
			
			
			Valitron\Valuser_idator::addRule( //проверяет есть ли такой емайл и соответствует ли правилам
				'unique',
				function($field, $value, array $params) use ($db){
					$sql="
						SELECT Count(*) count
						FROM users
						WHERE email=:email
					";
					$query=$db->prepare($sql); // создает объект запроса на основе sql, создает заготовку для него и записывает в куери
					$query->execute(['email'=>$value]); // заполняет заданнными значениями и выводит в виде асоциативного массива, но никуда не записывает
					$record=$query->fetch(); // извлекает одну запись из результата выполнения запроса
					return $record['count']==0; // сравниваем равно ли наша единственная запись нулю
				},
				'Пользователь с таким именем уже существует'
			);
			$valuser_idator=new Valitron\Valuser_idator($_POST); // проверяет насколько правильные пришли данные от пользователя
			$valuser_idator->rule( //!!
				'unique', // добавляем уникальный емайл
				'email'
			);
			$valuser_idator->rule( // вызвали объект валидатора
				'required', // требует не пустые поля
				['name','email'] // массив из двух строк
			)->message(
				'Поле обязательно для заполнения'
			);
			
			$valuser_idator->rule(
				'email', // название валидатора по которому проверяем 
				'email' // поле которое проверяется
			)->message( // уточняем сообщение которое выводить
				'Неверный формат e-mail!!!'
			);
			
			if ($valuser_idator->valuser_idate()){ // проверяем с учетом всех правил правильные ли данные или нет
				
				$sql="
				INSERT INTO `user` 
				(`name`, `email`) 
				VALUES (:name, :email);
				"; // вводим данные в базу
				$query=$db->prepare($sql);
				$result=$query->execute([ //выполняем запрос
					'name'=> $_POST['name'],
					'email'=> $_POST['email'],
				]); 
				if ($result){ //если запрос выполнен, то выводим
				//выполнения действий формы
				echo  '<div class="alert alert-success" role="alert">Сохранено</div>';
				} else {
				var_dump($query->errorInfo()); //вызовем эту команду, если запрос не выполнился и увидим в чем проблема
				}
			} else { // делает если данные не правильные
				$errors=$valuser_idator->errors(); // запрвшиваем у валидатора список ошибок
				$user=$$_POST;
				require 'form.php'; // вызываем файл
			}
		} else {
			if (isset($_GET['user_id'])) {
				// загрузка данных из БД
				$sql='Select * From user
								Where user_id=:user_id';
				$query=$db->prepare($sql);
				$query->execute([ 'user_id'=> $_GET['user_id']]); 
				$user=$query->fetch();
				if ($user) {
					require 'form.php';
				} else {
					require 'notfound.php';
				}
			} else {
				require 'form.php';// отображение пустой формы
			}
			
		}			
?>
</body>
</html>	