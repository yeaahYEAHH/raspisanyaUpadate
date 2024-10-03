function get( url, func ) {
	const xhr = new XMLHttpRequest();
	const token = "zyjcCdbyMVfRLEaMoLknOPePQkT6vLx7";

	xhr.open("GET", url, true);
	xhr.setRequestHeader("Authorization", `Bearer ${token}`);
	xhr.onreadystatechange = function () {
		if (xhr.status === 200 && xhr.readyState == xhr.DONE) {
			let data = JSON.parse(xhr.responseText);
				func( data )
		} else {
			console.log(`Код состояния: ${xhr.status}\nТекст: ${xhr.responseText}`);
		}
	};
	xhr.send();
}

function renderSchedule(data){

  document.getElementById('type').innerHTML = data.type;

  let duration = 100;

  function timer(){
	if(duration <= 0){
		get(`http://rest.loc/${document.title}/current`, renderSchedule);
		clearInterval(interval);
	}

	let min = ~~(duration / 60),
		sec = duration % 60;

	min = min >= 10 ? min : `0${min}`;
	sec = sec >= 10 ? sec : `0${sec}`;
	
	document.getElementById('timer').innerHTML = `${min}:${sec}`;
	duration--;
  }

  let interval = setInterval( timer, 1000)
}

function renderBirthday( data ){
	document.querySelector("#third").style.display = "flex";
	let names = document.querySelector('#names');

	for (let i in data) {
		names.innerHTML += `<h5 id="name" class="gradient__text">${data[i].Name}</h5>`;
	}


	let content = document.querySelector('.birthday__content');
	let name = document.querySelectorAll("#name");

	content.style.height =  `${name[0].offsetHeight}px`;

	let top = 0
	
	function slide(){
		top = top >= (name.length - 1) * name[0].offsetHeight ? 0 : top + name[0].offsetHeight;
		names.style.top = `-${top}px`;
	}

	setInterval(slide, 5000);
}

get(`http://rest.loc/${document.title}/current`, renderSchedule);
get(`http://rest.loc/birthday/current`, renderBirthday);