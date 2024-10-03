async function reponse( url ) {
    let date = new Intl.DateTimeFormat("ru", {
        timeZone: "Asia/Yekaterinburg",
        year: "numeric",
        day: "2-digit",
        weekday: "long",
        month: "long",
        hour: "numeric",
        minute: "numeric",
        second: "numeric"
    }).format(new Date());

    let option = {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer zyjcCdbyMVfRLEaMoLknOPePQkT6vLx7",
        },
    };

    const response = await fetch(url, option);

    if(!response.ok){
        response.text().then(data => console.log(data));
        return;
    }

    if (response.status == "201") {
        response.text().then(data => console.log(`${date}: ${data} по url ${url}`));
    }
}

;(function backup() {
    let paths = ['birthday', 'scheduleMonday', 'scheduleLDK', 'scheduleUAK', 'scheduleUPK', 'temp'];

    for (const item of paths) {
        let url = `http://rest.loc/${item}/backup`;

        reponse(url)
    }
})();

