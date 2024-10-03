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

function render(data) {
    document.getElementById("list").innerHTML = "";
    data.then((object) => {
      for (item of object) {
        document.getElementById("list").innerHTML += `
    <div class="row fs-5 p-1">
      <p class="col-md-auto mb-0"><input class="form-check-input item-checkbox" type="checkbox" value="${item.ID}" id="checkAll"/></p>
      <p class="col-1 mb-0" id="item${item.ID}">${item.ID}</p>
      <p class="col mb-0" id="item${item.ID}">${item.timeStart}</p>
      <p class="col mb-0" id="item${item.ID}">${item.timeEnd}</p>
      <p class="col mb-0" id="item${item.ID}">${item.type}</p>
    </div>
    `;
      }
    });
}

// обновить
function refresh(){
    let department = document.querySelector(".btn-check:checked").value;

    let field = 'ID',
        order = '1', 
        url = `http://10.0.4.147/${department}/sort?field=${field}&order=${order}`;

    let data = reponse(url);

    render(data);
}

document.querySelector("#refresh").addEventListener("click", refresh);
refresh();

document.querySelectorAll(".btn-check").forEach((item) => {
  item.addEventListener("change", () => {
    refresh();
  });
});

// 
// удаление
let btnDelete = document.querySelector("#btnDelete");
btnDelete.addEventListener("click", () => {
    let department = document.querySelector(".btn-check:checked").value;

    let temp = [];

    let url = `http://10.0.4.147/${department}/delete`;

    document.querySelectorAll(".item-checkbox:checked").forEach((item) => {
        temp.push(Number(item.value));
    });

    if(temp.length > 10){
        alert("Ошибка: не более 10 элементов")
    }

   reponse(url, `[${temp.join(',')}]`, "DELETE");
});

// 
// добавление
let btnAdd = document.querySelector("#btnAdd"),
    inputTimeStartAdd = document.querySelector("#inputTimeStartAdd"),
    inputTimeEndAdd = document.querySelector("#inputTimeEndAdd"),
    inputTypeAdd = document.querySelector("#inputTypeAdd"); 

btnAdd.addEventListener("click", () => {
    let department = document.querySelector(".btn-check:checked").value;

    let url = `http://10.0.4.147/${department}/add`;

    let obj = {
        timeStart: inputTimeStartAdd.value,
        timeEnd: inputTimeEndAdd.value,
        type: inputTypeAdd.value,
    };

    reponse( url, JSON.stringify(obj), 'POST');
});

// 
// сортировка
let btnSort = document.querySelector("#btnSort"),
  selectFieldSort = document.querySelector("#selectFieldSort"),
  sortType = document.querySelector("#sortType"),
  order = true;

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
  let department = document.querySelector(".btn-check:checked").value;  

  let url = `http://10.0.4.147/${department}/sort?field=${selectFieldSort.value}&order=${order}`;

  let data = reponse(url);

  render(data);
});

// 
// редактирование
let btnEdit = document.querySelector("#btnEdit"),
    IDEdit = document.querySelector("#IDEdit"),
    inputTimeStartEdit = document.querySelector("#inputTimeStartEdit"),
    inputTimeEndEdit = document.querySelector("#inputTimeEndEdit"),
    inputTypeEdit = document.querySelector("#inputTypeEdit"),
    btnOpenEdit = document.querySelector("#btnOpenEdit");

btnOpenEdit.addEventListener("click", () => {
    let id = document.querySelector(".item-checkbox:checked").value;
    inputValues = document.querySelectorAll(`#item${id}`);

    IDEdit.textContent = inputValues[0].textContent;
    inputTimeStartEdit.value = inputValues[1].textContent;
    inputTimeEndEdit.value = inputValues[2].textContent;
    inputTypeEdit.value = inputValues [3].textContent;
});
  

btnEdit.addEventListener("click", () => {
    let department = document.querySelector(".btn-check:checked").value; 

    let url = `http://10.0.4.147/${department}/edit`;

    let obj = {
        ID: Number(IDEdit.textContent),
        obj: {
            timeStart: inputTimeStartEdit.value,
            timeEnd: inputTimeEndEdit.value,
            type: inputTypeEdit.value,
            ID:  Number(IDEdit.textContent),
        }
    }

    reponse(url, JSON.stringify(obj), "PUT");
})

// 
// восстановление
let btnRecovery = document.querySelector('#btnRecovery');

btnRecovery.addEventListener("click", () => {
    let department = document.querySelector(".btn-check:checked").value;  

    let url = `http://10.0.4.147/${department}/recovery`;

    reponse(url);
})