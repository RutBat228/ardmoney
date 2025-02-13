<?php
include "inc/head.php";
access();
AutorizeProtect();
global $connect;
global $usr;
?>
<head>
    <title>Страница пользователя</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user.css">
</head>
<?
// Константы для конфигурации
const DAYS_BEFORE_ZP_SHOW = 7;
const USER_IMAGES = [
    'RutBat' => 'user_RutBat.png',
    'Игорь' => 'user_Игорь.png',
    'kovalev' => 'user_Вова.png',
    'grisnevskijp@gmail.com' => 'user_Паша.png',
    'Юра' => 'user_Юра.png'
];

// Получение текущего года
$current_year = date('Y');
$current_month = date('m');

// Проверка параметра older для отображения данных за прошлый год
$display_year = isset($_GET['older']) ? $current_year - 1 : $current_year;
$year = substr($display_year, -2); // Для совместимости с существующим кодом

// Оптимизация вывода изображения пользователя
function getUserImage(string $username): string {
    return isset(USER_IMAGES[$username])
        ? "img/" . USER_IMAGES[$username]
        : "img/user_logo.webp?123";
}

// Логика обработки даты из olduser.php
$month = null;
$date_blyat = null;

if (isset($_GET['date'])) {
    $month = date_view($_GET['date']);
    $date_blyat = $_GET['date'];
} else {
    $month = date('m');
    $year = date('y');
    $month = month_view(date('m'));
    $date = date("Y-m-d");
    $date_blyat = substr($date, 0, -3);
}

$year = date('y');
$year_cur = date('Y');
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-custom">
    <div class="container-fluid navbar-container">
        <a class="navbar-brand" href="#"></a>
        <div class="navbar-collapse" id="navbarNavDarkDropdown">
            <ul class="navbar-nav navbar-nav-custom" style="padding: 0.5rem 0;">
                <li class="nav-item dropdown" style="display: flex; align-items: center; justify-content: center;">
                    <a class="nav-link dropdown-toggle" href="#" style="padding-left: 2rem;" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $month ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="navbarDarkDropdownMenuLink" style="position: absolute; left: 100%; transform: translateX(-50%); min-width: 200px;">
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-01<?= isset($_GET['older']) ? '&older' : '' ?>">Январь</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-02<?= isset($_GET['older']) ? '&older' : '' ?>">Февраль</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-03<?= isset($_GET['older']) ? '&older' : '' ?>">Март</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-04<?= isset($_GET['older']) ? '&older' : '' ?>">Апрель</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-05<?= isset($_GET['older']) ? '&older' : '' ?>">Май</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-06<?= isset($_GET['older']) ? '&older' : '' ?>">Июнь</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-07<?= isset($_GET['older']) ? '&older' : '' ?>">Июль</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-08<?= isset($_GET['older']) ? '&older' : '' ?>">Август</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-09<?= isset($_GET['older']) ? '&older' : '' ?>">Сентябрь</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-10<?= isset($_GET['older']) ? '&older' : '' ?>">Октябрь</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-11<?= isset($_GET['older']) ? '&older' : '' ?>">Ноябрь</a></li>
                        <li><a class="dropdown-item" href="?date=<?= $display_year ?>-12<?= isset($_GET['older']) ? '&older' : '' ?>">Декабрь</a></li>
                    </ul>
                </li>
                <?php
                if (!empty(htmlentities($_COOKIE['user']))) {
                ?>
                    <div style="margin-left: auto; display: flex; align-items: center; gap: 1rem;">

                        <a href="search_montaj.php">
                            <img src="/img/search.png" alt="Поиск" style="width: 42px; height: 42px;">
                        </a>
                        <a href="user.php">
                            <img src="/img/home.png" alt="Домой" style="width: 42px; height: 42px;">
                        </a>
                    </div>
                <?php
                } ?>
            </ul>
        </div>
    </div>
</nav>
<ul class="list-group">
    <li class="list-group-item" style="padding: 0; border: none;">

        <?
        // Используем функцию getUserImage для получения пути к изображению
        $imagePath = getUserImage($usr['name']);
        echo '<img class="mx-auto d-block w-100" src="' . $imagePath . '" alt="Фото пользователя">';

        // Отображение алерта для данных прошлого года
        if (isset($_GET['older'])) {
            echo '<div class="alert alert-warning text-center" role="alert">
                <strong>Внимание!</strong> Вы просматриваете данные за ' . ($current_year - 1) . ' год
            </div>';
        }
        if ($usr['admin'] == "1" || $usr['name'] == "RutBat") {
        ?>
            <table class="table user-table">
                <thead>
                    <tr>
                        <th scope="col">Техник</th>
                        <th scope="col">Монтажи</th>
                        <th scope="col">Сумма денег</th>
                    </tr>
                </thead>
                <tbody class="user-table-gradient">
                    <?


                    $stmt = $connect->prepare("SELECT * FROM `user` WHERE `region` = ? ORDER BY `id` DESC");
                    $stmt->bind_param('s', $usr['region']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($tech = $result->fetch_assoc()) {


                    ?>
                        <tr>
                            <td><a style="color: black;font-weight: 700;font-size: small;" href="index.php?current_user=<?= $tech['fio'] ?>"><?= $tech['fio'] ?></a></td>
                            <td><?
                                num_montaj("$tech[fio]", "$month", $display_year);
                                ?></td>
                            <td><?
                                summa_montaj("$tech[fio]", "$month", $display_year);
                                ?> р.
                                <?php
                                // Получаем текущую дату
                                $currentDate = new DateTime();

                                // Получаем текущий месяц в числовом формате
                                $currentMonth = intval($currentDate->format('n')); // n - формат месяца без ведущего нуля

                                // Массив, свзывающий текстовые названия месяцев с их числовыми представлениями
                                $monthNames = [
                                    'Январь' => 1,
                                    'Февраль' => 2,
                                    'Март' => 3,
                                    'Апрель' => 4,
                                    'Май' => 5,
                                    'Июнь' => 6,
                                    'Июль' => 7,
                                    'Август' => 8,
                                    'Сентябрь' => 9,
                                    'Октябрь' => 10,
                                    'Ноябрь' => 11,
                                    'Декабрь' => 12,
                                ];

                                // Получаем числовое представление выбранного месяца (предполагается, что $month это текстовое название месяца)
                                $selectedMonth = $monthNames[$month] ?? 0; // Если месяц не найден в массиве, по умолчанию 0

                                // Получаем последний день текущего месяца
                                $lastDayOfMonth = new DateTime('last day of this month');

                                // Вычисляем разницу между текущей датой и последним днем месяца в днях
                                $daysUntilEndOfMonth = $currentDate->diff($lastDayOfMonth)->days;

                                // Если $month не является текущим месяцем и осталось менее ил равно 7 дням до конца месяца, либо $month отличается от текущего месяца, выполняем функцию prim_zp
                                if ($selectedMonth !== $currentMonth || $daysUntilEndOfMonth <= 7) {
                                    echo "<u><a style = 'color: #1ba11b;' href = 'zp.php?fio=$tech[fio]' >";
                                    prim_zp("$tech[fio]", "$month", $display_year);
                                    // Вызов функции для расчёта зарплаты за год

                                    echo '</a></u>';
                                }
                                ?>


                            </td>
                        </tr>
                    <?
                    }
                    ?>
                </tbody>
            </table>
        <?
        } else {
        ?>
            <table class="table" style="margin-bottom: 0rem;">
                <thead>
                    <tr>
                        <th scope="col">Техник</th>
                        <th scope="col">Монтажи</th>
                        <th scope="col">Сумма денег</th>
                    </tr>
                </thead>
                <tbody class="td_user">
                    <tr>
                        <td><?= $usr['fio']; ?></td>
                        <td style="color:red;"><?
                                                num_montaj("$usr[fio]", "$month", $display_year);
                                                ?></td>
                        <td><?
                            summa_montaj("$usr[fio]", "$month", $display_year);
                            ?> р.</td>
                    </tr>
                </tbody>
            </table>
        <?
        }

        // if ($usr['admin_view'] == 0) {
        ?>


        <?
if($usr['rang'] == "Мастер участка" OR $_COOKIE['user'] == "RutBat"){
            
            echo'
            <div class="alert alert-info" role="alert">
            <b><a href="gm.php">Admin ПАНЕЛЬ</a></b>
            </div>'; 
        }

        $connect->close();
        // echo'
        // <div class="alert alert-danger" role="alert">
        // <b><a href="404.html">404 test</a></b>
        // </div>'; 


        ?>


        <div class="container px-2" style="font-size: smaller; padding-top: 0.5rem; padding-bottom: 0.5rem;">
            <div class="row g-2">
                <div class="col text-center px-1">
                    <a href="user.php?older" class="btn btn-outline-dark btn-sm w-100" style="padding: 0.25rem 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2z"/>
                        </svg>
                        Суммы монтажей <br> <?= date('Y') - 1 ?>
                    </a>
                </div>
                <div class="col text-center px-1" style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                    <a href="index.php?older" class="btn btn-outline-dark btn-sm w-100" style="padding: 0.25rem 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-archive" viewBox="0 0 16 16">
                            <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                        </svg>
                        Архив монтажей <br> <?= date('Y') - 1 ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="alert alert-info" role="alert">
            Ваш логин: <b><?= $usr['name'] ?></b>
        </div>
        <div class="alert alert-success" role="alert" style="padding: 0px 20px 0px;">
            Приложение для Android <a href="ardmoney.apk" class="alert-link"><img src="img/android.png" style="width: 32px;padding-bottom: 18px;">ArdMoney</a>.
        </div>
        <div class="alert alert-light text-center text-muted" style="padding: 0.25rem; border-radius: 0; " role="alert">
            <?= $usr['region'] ?>
        </div>
        <div style="background: #000000cc;">
            <b><a href="/navigard">
                    <img src="img/navigard.png" style="
    width: 50%;
    padding: 10px;
"></a></b>
        </div>


        <b>
            <div class="d-grid gap-2">
                <a href="/exit.php" class="btn btn-outline-success btn-sm">Выход</a>
            </div>
        </b>
    </li>
</ul>
</div>
<?php include 'inc/foot.php'; ?>