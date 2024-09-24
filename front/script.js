function get( url, func ) {
	const xhr = new XMLHttpRequest();
	const token = "86e79ad061a7e8cd0d67a4f997ee6432e14652a281105239c49a225d789cc899";

	xhr.open("GET", url, true);
	xhr.setRequestHeader("Authorization", `Bearer ${token}`);
	xhr.onreadystatechange = function () {
		if (xhr.status === 200) {
			let data = JSON.parse(xhr.responseText);
      console.log(data)
			  // scheduleRender(data);
		} else {
			console.log(`Код состояния: ${xhr.status}\nТекст: ${xhr.responseText}`);
		}
	};
	xhr.send();
}

// const birthday = get("http://localhost:2000/birthday/current", birthdayRender);
const schedule = get(`http://localhost:2000/${document.title}/current`);

function timeRender() {
  let localTime = new Intl.DateTimeFormat("ru", {
    timeZone: "Asia/Yekaterinburg",
    hour: "numeric",
    minute: "numeric",
  }).format(new Date());

  document.getElementById("time").innerHTML = localTime;
}

setInterval(timeRender, 30000);

;(function dateRender() {
  let localDate = new Intl.DateTimeFormat("ru", {
    timeZone: "Asia/Yekaterinburg",
    year: "numeric",
    day: "numeric",
    weekday: "long",
    month: "long",
  });

  const dateParts = localDate
    .formatToParts(new Date())
    .filter((part) => part.type !== "literal");

  const [
    { value: weekday },
    { value: day },
    { value: month },
    { value: year },
  ] = dateParts;

    document.getElementById("day").innerHTML = day;
    document.getElementById("weekDay").innerHTML = weekday;
    document.getElementById("month").innerHTML = month;
    document.getElementById("year").innerHTML = year + " г.";

	timeRender();
})();


function subtractTime(time1, time2) {
  const [minutes1, seconds1] = time1.split(":").map(Number);
  const [minutes2, seconds2] = time2.split(":").map(Number);

  const totalSeconds1 = minutes1 * 60 + seconds1;
  const totalSeconds2 = minutes2 * 60 + seconds2;

  const resultSeconds = totalSeconds1 - totalSeconds2;

  if (resultSeconds < 0) {
    return "00:00";
  }

  const resultMinutes = Math.floor(resultSeconds / 60);
  const resultSecondsRemaining = resultSeconds % 60;

  document.getElementById("timer").innerHTML = `${String(resultMinutes).padStart(2, "0")}:${String(
    resultSecondsRemaining
  ).padStart(2, "0")}`;
}


function scheduleRender(obj){
  let localTime = new Intl.DateTimeFormat("ru", {
    timeZone: "Asia/Yekaterinburg",
    hour: "numeric",
    minute: "numeric",
  }).format(new Date());

  subtractTime(localTime, obj.timeEnd);
}



function birthdayRender(obj){
	document.querySelector("#third").style.display = "flex";

	for (let index in obj) {
    document.querySelector("#birthday").innerHTML += `
		<div class="birthdayName__card gradient__text">
		<img src="./assets//cake.svg" alt="cake">
		<p>${obj[index].Name}</p>
		</div>`;
  }
};


