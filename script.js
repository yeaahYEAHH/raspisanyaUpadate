let xhr = new XMLHttpRequest();

let birthday = false;
let content = document.querySelector('.birthdayName');
let step = 48;
let height = 0;

const date = new Date();
const cards = await document.querySelectorAll('.birthdayName__card');

const month = {
	0: 'Январь',
	1: 'Февраль',
	2: 'Март',
	3: 'Апрель',
	4: 'Май',
	5: 'Июнь',
	6: 'Июль',
	7: 'Август',
	8: 'Сентябрь',
	9: 'Октябрь',
	10: 'Ноябрь',
	11: 'Декабрь',
};

const weekDay = {
	0: 'воскресенье',
	1: 'понедельник',
	2: 'вторник',
	3: 'среда',
	4: 'четверг',
	5: 'пятница',
	6: 'суббота',
};

document.querySelector('#day').innerHTML = (date.getUTCDate() >= 10) ? date.getUTCDate() : `0${date.getUTCDate()}`;
document.querySelector('#year').innerHTML = `${date.getFullYear()} год`;
document.querySelector('#month').innerHTML = month[date.getUTCMonth()];
document.querySelector('#weekDay').innerHTML = weekDay[date.getUTCDay()];

function insertBirthdayName(listName){
	if(listName.length == 0){
		return
	}
	birthday = true;
	
	let birthdayBlock = document.querySelector('#third');

	birthdayBlock.style.display = 'flex'
	for(let index in listName){
		document.querySelector('#birthday').innerHTML += `
		<div class="birthdayName__card gradient__text">
		<img src="./assets//cake.svg" alt="cake">
		<p>${listName[index]}</p>
		</div>`
	}
};

function confett(){
	if (birthday){
		confetti({
			particleCount: 200,
			spread: 1000,
			origin: { y: 0, x: 0.5},
		})
	}
};

function slide(){
	height = height >= ((cards.length - 1) * step) ? 0 : height + step;
	content.style.top = `${-height}px`
};

function time(){
	const time = new Date(),
		hour = (time.getHours() >= 10) ? time.getHours().toString() : `0${time.getHours()}`,
		min = (time.getMinutes() >= 10) ? time.getMinutes().toString() : `0${time.getMinutes()}`,
		realTime = `${hour}:${min}`;

	document.querySelector('#time').innerHTML = realTime;
};

function actualLesson(){
	xhr.open('POST', 'https://neftpk.ru/schedule/server.php', true);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

	xhr.onreadystatechange = function update() {
		if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
			let obj = JSON.parse(xhr.responseText),
			 	objLesson = obj['lesson'],
				objBirthday = obj['birthday'];

			insertBirthdayName(objBirthday);
			if(objLesson === null){
				return
			}	
			document.getElementById('timer').innerHTML = obj['duration'];
			document.getElementById('type').innerHTML = objLesson.type;
			
		}
	};

	let title = date.getUTCDay() == 1 && document.title == "UAK" ?  "Monday" : document.title;
	xhr.send(`action=getActuality&title=schedule${title}`);
};


setInterval(confett, 6000);
setInterval(slide, 2000);
setInterval(time, 1000);
setInterval(actualLesson, 1000);
