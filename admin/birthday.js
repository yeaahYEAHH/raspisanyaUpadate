function render(data) {
    document.getElementById("list").innerHTML = "";
    data.then((object) => {
      for (item of object) {
        document.getElementById("list").innerHTML += `
    <div class="row fs-5 p-1">
      <p class="col-md-auto mb-0"><input class="form-check-input item-checkbox" type="checkbox" value=${item.ID}></p>
      <p class="col-1 mb-0" id="item${item.ID}">${item.ID}</p>
      <p class="col mb-0" id="item${item.ID}">${item.Name}</p>
      <p class="col mb-0" id="item${item.ID}">${item.Date}</p>
    </div>
    `;
      }
    });
}

async function reponse( url, body = '', method = "GET") {
    let option = {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer zyjcCdbyMVfRLEaMoLknOPePQkT6vLx7",
        },
    };

    if(method != "GET"){
        option = {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer zyjcCdbyMVfRLEaMoLknOPePQkT6vLx7",
            },
            body: body
        };
    }

    const response = await fetch(url, option);

    if(!response.ok){
        response.text().then(data => alert(data));
        return;
    }

    if (response.status == "201") {
        response.text().then(data => alert(data));
    }

    const data = response.json();
    return data;
}

function refresh(){
    let field = 'ID',
        order = '1',
        url = `http://10.0.4.147/${document.title}/sort?field=${field}&order=${order}`;

    let data = reponse(url);
    
    render(data);
}


document.querySelector("#refresh").addEventListener("click", refresh);
refresh();

// 
// удаление
let btnDelete = document.querySelector("#btnDelete");
btnDelete.addEventListener("click", () => {
    let temp = [];

    let field = 'ID',
        order = '1',
        url = `http://10.0.4.147/${document.title}/delete`;

    document.querySelectorAll(".item-checkbox:checked").forEach((item) => {
        temp.push(Number(item.value));
    });

    if(temp.length > 10){
        alert("Ошибка: не более 10 элементов")
    }

   reponse(url, `[${temp.join(',')}]`, "DELETE");
});

// 
//добавление
let btnAdd = document.querySelector("#btnAdd"),
  inputNameAdd = document.querySelector("#inputNameAdd"),
  inputDateAdd = document.querySelector("#inputDateAdd");

btnAdd.addEventListener("click", () => {
    let url = `http://10.0.4.147/birthday/add`;

    let obj = {
        Name: inputNameAdd.value,
        Date: inputDateAdd.value
    };

    reponse( url, JSON.stringify(obj), 'POST');
});

// 
// сортировка
let btnSort = document.querySelector("#btnSort"),
  selectFieldSort = document.querySelector("#selectFieldSort"),
  order = true;

selectFieldSort.addEventListener("change", () => {
  document
    .querySelectorAll("#radio")
    .forEach((item) => item.classList.remove("active"));
  if(selectFieldSort.value == "Date"){
     document
       .querySelectorAll("#radio")
       .forEach((item) => item.classList.add("active"));
  }
})

sortType.addEventListener("click", () => {
  if (sortType.childNodes[1].classList.contains("fa-arrow-up-a-z")) {
    order = false;
    sortType.childNodes[1].classList.remove("fa-arrow-up-a-z");
    sortType.childNodes[1].classList.add("fa-arrow-down-z-a");
  } else {
    order = true;
    sortType.childNodes[1].classList.remove("fa-arrow-down-z-a");
    sortType.childNodes[1].classList.add("fa-arrow-up-a-z");
  }
});

btnSort.addEventListener("click", () => {
    let date = document.querySelector(".radio:checked").value;

    let url = `http://10.0.4.147/${document.title}/sort?field=${selectFieldSort.value}&order=${order}`;

    if(selectFieldSort.value == "Date"){
        url = `http://10.0.4.147/${document.title}/sort?field=${selectFieldSort.value}&&order=${order}$date=${date}`
    }

    let data = reponse(url);

    render(data);
});

// 
// поиск
let btnSearch = document.querySelector("#btnSearch"),
  selectSearch = document.querySelector("#selectSearch"),
  inputSearch = document.querySelector("#inputSearch");

btnSearch.addEventListener("click", () => {
  let url = `http://10.0.4.147/${document.title}/search?field=${selectSearch.value}&value=${inputSearch.value}`;

  let data = reponse(url);

  render(data);
});


// 
// Редактировать 
let btnEdit = document.querySelector("#btnEdit"),
    IDEdit = document.querySelector("#inputIDEdit"),
    inputNameEdit = document.querySelector("#inputNameEdit"),
    inputDateEdit = document.querySelector("#inputDateEdit"),
    btnOpenEdit = document.querySelector("#btnOpenEdit");



btnOpenEdit.addEventListener("click", () => {
    let id = document.querySelector(".item-checkbox:checked").value;
    inputValues = document.querySelectorAll(`#item${id}`);

    IDEdit.textContent = inputValues[0].textContent;
    inputDateEdit.value = inputValues[2].textContent;
    inputNameEdit.value = inputValues[1].textContent;
});

btnEdit.addEventListener("click", () => {
    let url = `http://10.0.4.147/${document.title}/edit`;

    let id = document.querySelector(".item-checkbox:checked");

    console.log(id);

    let obj = {
        ID: Number(IDEdit.textContent),
        obj: {
            Name: inputDateEdit.value,
            Date: inputNameEdit.value,
            ID: Number(IDEdit.textContent)
        }
    }

    reponse(url, JSON.stringify(obj), "PUT");
});

// 
// восстановление
let btnRecovery = document.querySelector('#btnRecovery');

btnRecovery.addEventListener("click", () => {
    let url = `http://10.0.4.147/${document.title}/recovery`;

    reponse(url);
})

