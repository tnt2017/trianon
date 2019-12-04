<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Language" content="ru">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <title>Журнал счетов</title>
  <link rel=stylesheet type="text/css"  href="default.css">
  <link rel=stylesheet type="text/css"  href="js/dyna_cal.css">
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="icon" href="images/favicon.ico">
  <style type="text/css">
    .kk { background-color: #FF8080;}
    .mod {background-color: yellow; }
    .idd { color: brown; cursor: pointer; font-weight: bold; }
    .dat { width: 11ex; }
    .rsv { background-color: #C0C0C0 }
    .dt { background-color: lightblue }
    .tt { background-color: #e5e5e5; text-align: right; }
    .btn_exp { padding: 4px; }

    .doc_row {
        display: block;
        width: 99%;
        border: 1px solid black;
        overflow: hidden;
        text-align: center;
    }
    .doc_row div {
        display: inline-block;
        padding: 4px;
        text-align: center;
        width: auto;
    }


    div#enldlg5 { position: absolute; display: none; border: 2px solid grey; background: lightgrey; z-index: 7; }

    .pop-block {
  display: inline-block;
  position: relative; /* указывать обязательно */
  width: 50%;
  height: 300px; /* высота всего блока */
  margin: 1%;
}
.close-block {
  display: block;
  position: absolute;
  top: 8px;
  right: 8px;
  width: 16px;
  height: 16px;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAElBMVEXqAAD8oaH/AAD/ra3/vr7///91+I/7AAAAQklEQVR42oXPwQoAIAgD0JXu/385FfTQgnYweKAp9pUXmE+swAFWAG8owQBTqjQQ8SQOcAdRQVtkqHy7dDFd/X/tAVqqAopyUfkOAAAAAElFTkSuQmCC);
  cursor: pointer;
}
.pop-block p {
  width: 100%;
  height: auto;
}


#pop-checkbox {
  display: none;
}




  </style>
  <script src="js/dates.js" ></script>
  <script src="js/jquery-1.12.0.min.js"></script>
  <script src="js/dyna_cal.js" ></script>
  <script src="js/jqlib.js?v=6" ></script>

<script src='js/jquery-1.3.2.min.js'></script>



<script>

function handle(object, price){

     //alert(object.innerText);
     //var s=prompt("введите новое значение: ",price);

     //alert(object.innerText);
     var inp = document.createElement("input");
     inp.type = "text";
     inp.value = object.innerText;
     inp.size=3;

     object.innerText = "";
     object.appendChild(inp);
     
     var _event = object.onclick;
     object.onclick = null;

     inp.onkeydown = function(e){

      alert(e.keyCode);

         if(e.keyCode == 13) //=
         {
               object.innerText = inp.value;

               alert(object.innerText);
               //alert(object.nextSibling.innerText);

               object.onclick = _event;
               object.removeChild(inp);
         }

     };

     inp.onblur = function(e)
     {       
      object.innerText = inp.value;
               object.onclick = _event;
               object.removeChild(inp);
     };*/
}

</script>





</head>
















































<body>


  <div id="page_overlay"></div>
  <div class=c><span class=head>Журнал счетов</span><br>
      [<a href="default.php">Главная</a>]</div>
  <hr>



  <form method=post target=_self  name=frmMain id="frmMain" >.


  <input type="button" class="btnS" value="Показать" onclick="load_journals()">
  <input type="button" class="btnS" value="Показать doc" onclick="load_doc(190109645)">

  

  <input type=hidden name=sRight value='3' >
  <script src="js/dyna_calco.js"></script>
  <table border=0 cellspacing=5>
  <tr><td>Дата :</td><td>
      <input name=datBeg class=dat value='<?php echo $_REQUEST[datBeg]; ?>' onChange="this.value=CheckDate(this.value);">
      <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datBeg')">

      <input name=datEnd class=dat value='<?php echo $_REQUEST[datEnd]; ?>' onChange="this.value=CheckDate(this.value);">
      <img src="images/cal.gif" width=16 height=16 onclick="ds_sh('datEnd')">
      </td><td></td>
  </tr><tr>
    <td>Торгпред :</td><td><select name=cbEmp size=1>
<option value=1800028 > Ёкубов Д.С.<option value=500 > Ёкубов Т.Г.<option value=1433 > Ёкубов Х.Г.<option value=1900102 > Абдурахимова З.Д.<option value=1900049 > Агарков В.Н.<option value=3167 > Адова О.В.<option value=3346 > Азимджонов Ш.Ш.<option value=1900004 > Акимова С.С.<option value=1900063 > Алеев И.С.<option value=2124 > Алиева Л.Ф.<option value=2953 > Альшевская  Т.А.<option value=1900075 > Анашкин М.Н.<option value=3422 > Андреев В.Г.<option value=1900064 > Андрианова Э.В.<option value=1900118 > Арабок Н.Н.<option value=2159 > Аракулов  Р.Х.<option value=2810 > Атясова В.В.<option value=3488 > Ахмадулина Т.И.<option value=2578 > Ахунжонов О.А.<option value=3489 > Бабенко М.Е.<option value=1900057 > Бабушкин В.С.<option value=2694 > Балабанов Н.А.<option value=1900080 > Балакирев П.В.<option value=1900016 > Баранник В.А.<option value=3258 > Барсуков П.А.<option value=1900113 > Басалаева В.А.<option value=1597 > Баскакова Д.С.<option value=3219 > Баталова О.Ф.<option value=3102 > Бафер С.В.<option value=1900052 > Бахарев А.Н.<option value=3276 > Безрукова (Зайцева) К.А.<option value=3303 > Беликова Е.Г.<option value=1943 > Белоус И.В.<option value=1900021 > Берсенев Е.А.<option value=3362 > Бирклен А.В.<option value=1800164 > Богданов Д.С.<option value=1900026 > Бондаренко Р.С.<option value=1900011 > Брюханов О.Д.<option value=1900151 > Буикли М.Н.<option value=1900135 > Буикли Ю.Н.<option value=1900039 > Бурксер В.Ф.<option value=1900069 > Бусыгин Э.А.<option value=1900148 > Быкова О.О.<option value=1697 > В.Камень-на-Оби ..<option value=1613 > В.Татарск ..<option value=1900099 > Вайнммайер А.А.<option value=1900032 > Васильев В.Г.<option value=1800034 > Великанов А.В.<option value=2030 > Волков С.А.<option value=3479 > Вольхин Ю.И.<option value=1900173 > Воронин В.А.<option value=1900051 > Воронцова О.Н.<option value=2974 > Вылегжанина Л.А.<option value=2392 > Гаврилова А.А.<option value=1589 > Гаврилюк Т.В.<option value=3295 > Гапонова  Е.С.<option value=1900094 > Гармаш М.И.<option value=3080 > Гасанов Э.Г.<option value=1900180 > Гаюров С.С.<option value=3145 > Генрих Е.В.<option value=1900046 > Гладышев  П.Б.<option value=3408 > Голубцов Р.С.<option value=1900126 > Гордеева Я.Б.<option value=3407 > Григорьев Д.А.<option value=1900114 > Григорьев С.В.<option value=1900167 > Громова И.А.<option value=1800147 > Губарев Е.Е.<option value=1900007 > Губин  Ю.Н.<option value=1356 > Гузенко М.В.<option value=23 > Гурин К.В.<option value=3354 > Гусев Д.В.<option value=2137 > ДЕГТЯРЕВ К.А.<option value=1800066 > Дащинский И.В.<option value=1903 > Дегтярев В.В.<option value=1381 > Дегтярева К.С.<option value=3325 > Дементьев С.В.<option value=1800178 > Денисов Д.А.<option value=1900119 > Джежула И.В.<option value=1900107 > Диких А.Ю.<option value=3338 > Диптан М.К.<option value=1800080 > Довженко А.В.<option value=2445 > Додов Я.Х.<option value=1900054 > Дорошенко Р..<option value=3367 > Драничникова М.Н.<option value=2687 > Дусмуратов А.Т.<option value=1900134 > Дусмуратов мл А.Т.<option value=1800059 > Душаткина Ж.П.<option value=3178 > Екименко Е.И.<option value=2889 > Епанчинцева  К.С.<option value=1900125 > Епифанов А.А.<option value=1800156 > Ерлинеков А.Г.<option value=3484 > Ерлинекова Д.Г.<option value=3440 > Ерофеев А.В.<option value=2661 > Жданова С.В.<option value=1800110 > Жеребор О.В.<option value=1900112 > Живетьев К.В.<option value=1800074 > Жукова В.В.<option value=1800054 > ЗАЯЦ П.М.<option value=1900146 > Загуменнова В.Н.<option value=1900018 > Зайцева Е.С.<option value=2170 > Зингаев А.В.<option value=2898 > Зинченко Н.В.<option value=1900170 > Зможная Е.А.<option value=1900081 > Зубков И.И.<option value=3261 > Иванов Е.С.<option value=1900162 > Иванова Э.А.<option value=1900136 > Иванцов Д.А.<option value=1393 > Иващенко Л.В.<option value=1900155 > Игнатовский С.И.<option value=1367 > Исакова Ю.Г.<option value=1900059 > Исмаилова Е.В.<option value=2995 > Ишкулов Р.Н.<option value=3275 > Камбаралиева Н.Э.<option value=3165 > Камбулатов С.У.<option value=3079 > Каракулина  А.А.<option value=1900088 > Карасев В.А.<option value=1900158 > Каратаев П.Г.<option value=1800169 > Карлсон Д.П.<option value=1624 > Карманова О.Г.<option value=1800118 > Карпова Н.А.<option value=3284 > Каур Н.В.<option value=1900153 > Каценко А.Б.<option value=2941 > Кириленко С.В.<option value=3218 > Кириллова Н.М.<option value=1900127 > Клемешов Д.Ф.<option value=2748 > Климкин О.Г.<option value=1800049 > Клинг И.Н.<option value=2586 > Кобулова М.Д.<option value=3504 > Козодоев О.В.<option value=1900139 > Колидова П.Н.<option value=2481 > Колодочка А.В.<option value=2987 > Комарова Ю.В.<option value=1900047 > Копылов А.А.<option value=1900117 > Копылова  Ю.Е.<option value=2289 > Копытин В.В.<option value=1900017 > Корытина  Я.В.<option value=1900068 > Косый А.В.<option value=1900129 > Красуля А.А.<option value=3419 > Крохта А.И.<option value=1800048 > Кудряшов А.А.<option value=3324 > Кудряшов В.А.<option value=1900035 > Кузнецов А.Н.<option value=3425 > Кузнецов С.И.<option value=1900168 > Кузьмина В.Д.<option value=3193 > Кукуянцева Р.И.<option value=1900078 > Кулаков С.Ю.<option value=3290 > Куркина Л.П.<option value=3451 > Кухоренко Ю.Г.<option value=1800014 > Кучкоров Р.З.<option value=1900038 > Кушоков Х.Х.<option value=1900100 > Лазарева  П.С.<option value=1900005 > Лакаев Д.Д.<option value=1900110 > Лапшин А.Л.<option value=1900121 > Лебедева А.В.<option value=1800010 > Литвиненко И.Ю.<option value=1800076 > Лифанов А.А.<option value=1800029 > Лобков К.А.<option value=1900116 > Лободина Е.В.<option value=1900165 > Ломиковская Н.Я.<option value=1800078 > Лончаковская М.С.<option value=3234 > Ляпс А.С.<option value=1900175 > МАШРАПОВ Т.М.<option value=1800079 > МОТОРИН 5Т А.А.<option value=1800058 > Магдич М.Ф.<option value=1501 > Мазяр С.А.<option value=3134 > Малышева Д.С.<option value=1900086 > Мамадалиева Ш.Т.<option value=1900157 > Мамаразаев З.К.<option value=1800056 > Манжола   А.В.<option value=2680 > Марахонова  Я.А.<option value=3238 > Марахонова (Развитие) Я.А.<option value=1800043 > Мардоян Е.М.<option value=1500 > Махмудов Р.Ф.<option value=1800001 > Махмудова Е.Н.<option value=1800154 > Махтаев  В.В.<option value=1900073 > Машрапов Т.М.<option value=1900067 > Медведев Ю.Н.<option value=1800112 > Медведкова Н.В.<option value=1503 > Мерекин И.А.<option value=1900043 > Мизамидинова Г.И.<option value=3452 > Мозговой А.В.<option value=1900166 > Моисеев Д.Г.<option value=2117 > Москвина М.С.<option value=1900101 > Мотовилова С.В.<option value=2927 > Мотрюк Ю.Г.<option value=3337 > Мячина Т.В.<option value=2085 > НОВЫЙ ТоргПред ..<option value=1800086 > Наботов Д.И.<option value=1800011 > Нагайцев Ю.В.<option value=2864 > Нагибова  А.В.<option value=1900177 > Назин Д.В.<option value=1800124 > Нарзуллозода С.Н.<option value=1900071 > Насыров В.Х.<option value=1900096 > Наумкина С.В.<option value=1800157 > Нестерова А.О.<option value=3453 > Нестерюк В.В.<option value=3485 > Никитина Р.С.<option value=1900103 > Никифорова Л.А.<option value=2961 > Новикова А.А.<option value=3206 > Новожилова Ю.А.<option value=3142 > Нуралиев М.Ж.<option value=1900174 > Овчинников Д.С.<option value=2780 > Олимжон У.Т.<option value=3215 > Олимжон мл У.Е.<option value=2652 > Омурзакова  М.Ю.<option value=1800103 > Онищук Е.В.<option value=1900014 > Орипов  А.Э.<option value=1900074 > Орифов Ф.Н.<option value=1614 > Оспенников С.И.<option value=1900159 > Ощепков А.В.<option value=1900171 > ПАНКРАТОВ В.А.<option value=1236 > Панина Т.А.<option value=3409 > Панкратов В.А.<option value=2957 > Папшев И.И.<option value=2140 > Папшева  М.В.<option value=1800137 > Пашкова  О.А.<option value=1900077 > Пашкуров Р.А.<option value=1900082 > Перебейнос Д.А.<option value=1900092 > Петров В..<option value=3454 > Петров В.С.<option value=137 > Петров П.Г.<option value=1800109 > Петрова Е.Г.<option value=1900163 > Петрусевич Е.В.<option value=3428 > Петухов И.А.<option value=1314 > Пешков С.В.<option value=1900176 > Пигарев А.В.<option value=1900130 > Пластинина Е.Е.<option value=1900030 > Повышева Н.И.<option value=1800020 > Подоляко мл О.Б.<option value=1693 > Поздняков Д.В.<option value=1996 > Поливанов  В.В.<option value=1519 > Попков И.А.<option value=1900089 > Попов А.А.<option value=1900120 > Попов гр А.Л.<option value=1900142 > Потапова  Е.Н.<option value=1900105 > Потапова Ю.А.<option value=1900150 > Привалова И.С.<option value=1800153 > Придчина А..<option value=3314 > Проняева С.Е.<option value=1900137 > Пшеничный Д.С.<option value=1900111 > Рабчевский  Д.Г.<option value=2819 > Ратников  А.В.<option value=2970 > Рахимов  И.Д.<option value=1900028 > Решетников С.В.<option value=1800015 > Ризоева М.Н.<option value=1900079 > Рипли В.В.<option value=1900161 > Ростовцев А.Р.<option value=1900015 > Рузиев Х.Э.<option value=1800180 > Руппель А.А.<option value=3406 > Рыбин А.И.<option value=55 > Рылов А.А.<option value=1900178 > Рыманов Д.М.<option value=1800108 > Рычков А.С.<option value=1629 > СКС ..<option value=1150 > Сабанцева Ю.В.<option value=1800172 > Савёлова В.А.<option value=1800149 > Саворовский А.В.<option value=2327 > Сазонова  И.Ю.<option value=1900019 > Салиев Э.Э.<option value=1900132 > Саломатова С.В.<option value=1312 > Самойлов Ю.Н.<option value=1800119 > Самцов А.А.<option value=3456 > Сапелкин А.Н.<option value=2933 > Сарбаева  Е.В.<option value=3490 > Сафарова Б.Н.<option value=1900062 > Сафонова Г.А.<option value=3370 > Сафронова Е.Г.<option value=1900172 > Севрюков М.И.<option value=3476 > Сембаева Р.С.<option value=1900053 > Семенов Р.В.<option value=1900029 > Семионенко А.В.<option value=1900001 > Серебреникова И.В.<option value=1900093 > Сериков В..<option value=2926 > Сеттарова Х.Х.<option value=1900037 > Сиворонов В.А.<option value=1900070 > Сиворонов гр И.А.<option value=2744 > Сигорский А.А.<option value=3500 > Сидиков М.И.<option value=1900149 > Скуржинский А.В.<option value=1900179 > Сметанников В.В.<option value=2057 > Смородин Д.М.<option value=1302 > Смородин М.И.<option value=1900106 > Смыковский  Д.И.<option value=1900009 > Соколов М.Р.<option value=1515 > Соколова Я.Е.<option value=1800159 > Соломагина Д.А.<option value=2371 > Солярских В.А.<option value=1900124 > Сотников А.Ю.<option value=3122 > Ставский О.В.<option value=1362 > Стадниченко В.В.<option value=3071 > Стариков Е.М.<option value=1800008 > Стенина Е.В.<option value=2971 > Степанов С.Г.<option value=3384 > Столярчук Алтай М.М.<option value=1900055 > Сулаев М.Е.<option value=2840 > Султонова Х.О.<option value=2980 > Сыромятникова Т.Ю.<option value=3435 > Сычёва К.А.<option value=1900066 > Тарасов Барнаул П.В.<option value=2180 > Тарасов П.В.<option value=2108 > Тарасова А.С.<option value=1900058 > Твалодзе С.В.<option value=1900010 > Тендетников М.М.<option value=1900083 > Теплова Е.Ю.<option value=1900164 > Терехов А.К.<option value=1397 > Теркулова О.С.<option value=1900154 > Титков А.В.<option value=1900140 > Титова Т.В.<option value=1800171 > Тихонов С.Г.<option value=3229 > Тихонов Я.А.<option value=2924 > Тихонова Е.В.<option value=1900023 > Ткачева А.Г.<option value=1900147 > Толмачев А.Ю.<option value=1900090 > Толмачева П.О.<option value=1800025 > Траутвейн А.И.<option value=1900141 > Трифоненков Е.А.<option value=2802 > Тумайкина Т.П.<option value=1900156 > Туров В.В.<option value=1714 > Уразова С.Л.<option value=1900087 > Устинова Н.А.<option value=3297 > Фараджов Ф.Ф.<option value=1900042 > Филиппов А.О.<option value=1900145 > Фрайденберг Д.Ю.<option value=1900133 > Хаустов В.А.<option value=1900131 > Хурсенко И.К.<option value=2996 > Царенко Е.А.<option value=1900181 > Черных О.П.<option value=2869 > Черняк Н.М.<option value=3411 > Чечулин Р.Е.<option value=1800052 > Чиишева В.В.<option value=3501 > Чудинов С.А.<option value=2919 > Чудинова Е.А.<option value=1900076 > Чулков А.С.<option value=3457 > Чупров А.А.<option value=1900115 > Чушников А.А.<option value=2278 > Шадрин Алтай О..<option value=1557 > Шадрин О.В.<option value=1800041 > Шадрина И.А.<option value=1900084 > Шанина Н.П.<option value=2777 > Шарипова (Ермолаева) А.В.<option value=1900138 > Швец А.В.<option value=3431 > Швецов Ю.М.<option value=3432 > Шевелев Е.В.<option value=3412 > Шевелев И.Е.<option value=1900098 > Шестакова П.Д.<option value=2678 > Шибкая Л.Ю.<option value=1900169 > Шигорева А.А.<option value=3386 > Шикова В.В.<option value=2107 > Шипилова Е.В.<option value=1900104 > Широких А.П.<option value=1900085 > Широков С.Ю.<option value=2105 > Шишкин А.В.<option value=1900095 > Шкуратов А.Е.<option value=1900123 > Шпирко А.А.<option value=1800173 > Штабель А.А.<option value=2573 > Шумахер(для склада) В.М.<option value=1900024 > Щекочихин А.А.<option value=1852 > Юмашев И.В.<option value=2682 > ЯКУБОВ Ф.Г.<option value=1800167 > Яблоков С.В.<option value=1478 > Якубов С.Д.<option value=3214 > Якубов Ш.Ф.<option value=2549 > Якубов мл Д.М.<option value=3171 > Якубова М.А.<option value=1900008 > Яниос К.А.<option value=796 > Яппарова  Н.В.<option value=1900050 > Ярошенко А.А.      </select></td>

    </tr>

<tr>
  <td>Контрагент :</td>
  <td>
  <div class="easy-autocomplete" style="width: 400px;">
    <input name=sOrg value="" class=org autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="eac-5912">
    <div class="easy-autocomplete-container" id="eac-container-eac-5912"><ul style="display: none;"></ul></div></div>
  </td>

</tr>
  </table>

<h2>Счета</h2>
  <div id='div_scheta'></div>

<h2>Накладные в резерве</h2>
  <div id='div_rezervi'></div>


  <input type="checkbox" id="pop-checkbox"> <!--тег размещается непосредственно у блока-->
  <div id="enldlg5" style="left: 339.317px; top: 820px; display: block; padding: 20px;" class='pop-block'>

  <label for="pop-checkbox" class="close-block" onclick="$('div#enldlg5').hide();"></label> <!--кнопка закрытия; значение для for соответствует id у input-->


<h2>Документ</h2>
  <div id='doc_head'></div>
  <br>
  <div id='document'></div>

  </div>


</form>
<HR>
<p class=foot>
<br>Copyright &copy; 2019 [Trianon]. All rights reserved. <BR>
Revised: 26.11.2019



<script>

/* почему не работает ни один из этих способов очень интересно!!!!!!!!!!!!
  $('select option[value="3435"]).prop('selected', true);
  $("#cbEmp").val("3435").change()
  $("#cbEmp option[value='Mazda']").val('3435').attr("selected", true);
  $("#cbEmp").val("3435");
  $("select").val("").trigger("chosen:updated")
  $("#cbEmp").val(3435);
  alert('1');
*/



  var n= frmMain.cbEmp.options.length
          for(i=0; i < n; i++) {
            if( frmMain.cbEmp.options[i].value == <?php echo $_REQUEST[cag]; ?> ) {
              frmMain.cbEmp.selectedIndex= i; break }
          }

            document.addEventListener('click', function(event) {

            if(event.target.id!="")
            {
              var param=event.target.getAttribute("param");

              console.log('id=' + event.target.id);
              console.log('param=' + param);
              if(param=='prc')
              {
              console.log('цена=' + event.target.innerText);
              var new_price=prompt('Введите новую цену', event.target.innerText);
              }

              if(param=='kol')
              {
              console.log('цена=' + event.target.innerText);
              var new_kol=prompt('Введите новое количество', event.target.innerText);
              }
            }


            if (event.target.dataset.counter != undefined) 
            { // если есть атрибут...
            event.target.value++;
            }

          });


 

  function load_journal(imya_jurnala, div_name)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
    console.log('load_journal :: imya_jurnala=' + imya_jurnala);
    
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'journal' : 1,
            'journal_type' : imya_jurnala,
            'dtOn1' : frmMain.datBeg.value,
            'dtOn2' : frmMain.datEnd.value,
            'cag' : frmMain.cbEmp.value
          },
      success: function(dat, stat,xmlReq) 
      {
            console.log(dat);
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>ID</td>";
            s+="<td>DATA</td>";
            s+="<td>Номер</td>";
            s+="<td>Сумма</td>";
            s+="<td>Контрагент</td>";

            s+="<td>Адрес</td>";
            s+="<td>Примечание</td>";
            s+="<td>агент</td>";
            s+="<td>резерв</td>";
            s+="<td>x</td>";
            s+="<td>x</td>";

            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              if(Myobj[i].CRESERV=='1')
              s+="<tr bgcolor='gray'>";
              else
              s+="<tr>";
              s+="<td bgcolor='red' onclick=\"load_doc($(this), " + Myobj[i].ID + ")\">" + Myobj[i].ID + "</td>";
              s+="<td>" + Myobj[i].DATA + "</td>";
              s+="<td>" + Myobj[i].NNAKL + "</td>";
              s+="<td>" + Myobj[i].SUMMA + "</td>";
              s+="<td>" + Myobj[i].ORG + "</td>";

              s+="<td>" + Myobj[i].ADR + "</td>";
              s+="<td>" + Myobj[i].TXT + "</td>";
              s+="<td>" + Myobj[i].CAGENT + "</td>";
              s+="<td>" + Myobj[i].CRESERV + "</td>";
              s+="<td><input type=\"button\" value=\"Превратить в накладную\" onclick=\"SwitchNakl(" + Myobj[i].ID + ")\" ></td>";
              s+="<td><input type=\"button\" value=\"Удалить\" onclick=\"BillDelete(" + Myobj[i].ID + ")\" ></td>";
 
              s+="</tr>";
            }
            s+="</table>";

            console.log('выводим таблицу в слой ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) { alert("load_journal: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}

function load_journals()
{
  load_journal('journal_schetov', 'div#div_scheta');
  load_journal('journal_nakladnih', 'div#div_rezervi');
}

function load_doc(e, idDoc)
{
  load_doc_header(idDoc)
  load_doc_lines(idDoc)

  console.log('show_divdoc start');
       var off= e.offset();
       var x= off.left + 50;
       var y= off.top + e.height();
       dlg= $("div#enldlg5");
       dlg.css({left: x, top: y}).show();
       //dlg.css({visibility: visible}).show();

       //$("#enldlg5").css("visibility","visible"); 
       //$("#enldlg5").css("display","block"); 


       $("#pop-checkbox").val(true);
}

function load_doc_header(idDoc)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'get_doc_header': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            //alert(dat);
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>NNAKL</td>";
            s+="<td>DN</td>";
            s+="<td>AGENT</td>";
            s+="<td>OPER</td>";
            s+="<td>ORG</td>";
            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              s+="<tr>";
              s+="<td>" + Myobj[i].NNAKL + "</td>";
              s+="<td>" + Myobj[i].DN + "</td>";
              s+="<td>" + Myobj[i].AGENT + "</td>";
              s+="<td>" + Myobj[i].OPER + "</td>";
              s+="<td>" + Myobj[i].ORG + "</td>";
              s+="</tr>";
            }
            s+="</table>";

            var div_name="div#doc_head";
            //alert('выводим таблицу в слой ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) {   alert("load_doc_header: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}

function load_doc_lines(idDoc)
  {
    var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'get_doc_lines': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            var Myobj = JSON.parse(dat);  
            
            var s="<table border=1>";
            s+="<tr>";
            s+="<td>ID</td>";
            s+="<td>Товар</td>";
            s+="<td>Цена</td>";
            s+="<td>Количество</td>";
            s+="<td>Сумма</td>";
            s+="</tr>";

            for(var i=0; i<Myobj.length-1;i++)
            {
              var t=1;
              s+="<tr>";
              s+="<td>" + Myobj[i].CMC + "</td>";
              s+="<td>" + Myobj[i].NAME + "</td>";
              s+="<td bgcolor='#DAA520' id=" + Myobj[i].CMC + " param='prc'>" + Myobj[i].PRICE + "</td>"; //$(this)
              s+="<td bgcolor='#DAA520' id=" + Myobj[i].CMC + " param='kol'>" + Myobj[i].KOL + "</td>";
              s+="<td>" + Myobj[i].SUMMA + "</td>";
              s+="</tr>";
            }
            s+="</table>";

            var div_name="div#document";
            //alert('выводим таблицу в слой ' + div_name);
            $(div_name).html(s);
            //x.innerHTML=s;

    },
    error: function(xmlReq,stat, err) { alert("load_doc_lines: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });

}

function BillDelete(idDoc)
{
  var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'delete_doc': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            //alert(dat);
            console.log(dat);
      },
    error: function(xmlReq,stat, err) {   alert("BillDelete: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });

  load_journals();
}


function SwitchNakl(idDoc)
{
  alert(idDoc);

  var par='0e324d80f596bd4de2a13663g06f492be18ee3';
 
    $.ajax({
      url: "tnt_oracle_test.php",
      dataType: "html",
      data: {
            'par': par, 
            'switch_nakl': 1,
            'idDoc': idDoc
          },
      success: function(dat, stat,xmlReq) 
      {
            alert(dat);
            console.log(dat);
      },
    error: function(xmlReq,stat, err) {   alert("SwitchNakl: "+stat+" ajax : "+err)},
    complete: function(xmlReq,stat) {   }
  });
}


$( document ).ready(function() {
    console.log( "ready!" );
    load_journals();
});

</script>   

</body>
</html>

