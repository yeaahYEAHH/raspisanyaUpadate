async function reponse(body) {
  const response = await fetch("http://localhost:8000/server.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(body),
  });

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
  let body = {
    type: "get",
    department: department,
  };

  let data = reponse(body);
  render(data);
}

let btnRefresh = document.querySelector("#refresh");
btnRefresh.addEventListener("click", refresh);
refresh();

document.querySelectorAll(".btn-check").forEach((item) => {
  item.addEventListener("change", () => {
    refresh();
  });
});

// удаление
let btnDelete = document.querySelector("#btnDelete"),
  inputDelete = document.querySelector("#inputDelete");

btnDelete.addEventListener("click", () => {
  let department = document.querySelector(".btn-check:checked").value;
  let temp = [];
  document.querySelectorAll(".item-checkbox:checked").forEach((item) => {
    temp.push(item.value);
  });

  if (Array.isArray(temp) && temp.length !== 0) {
    for (let item of temp) {
      let body = {
        type: "delete",
        department: department,
        ID: item,
      };
      reponse(body);
    }
    return;
  }

  let body = {
    type: "delete",
    ID: inputDelete.value,
    department: department
  };
  reponse(body);
});

// добавить
let btnAdd = document.querySelector("#btnAdd"),
  inputTimeStartAdd = document.querySelector("#inputTimeStartAdd"),
  inputTimeEndAdd = document.querySelector("#inputTimeEndAdd"),
  inputTypeAdd = document.querySelector("#inputTypeAdd");

btnAdd.addEventListener("click", () => {
  let department = document.querySelector(".btn-check:checked").value;
  let body = {
    type: "add",
    department: department,
    obj: {
      timeStart: inputTimeStartAdd.value,
      timeEnd: inputTimeEndAdd.value,
      type: inputTypeAdd.value,
    },
  };

  reponse(body);
});

// сортировать
let btnSort = document.querySelector("#btnSort"),
  selectFieldSort = document.querySelector("#selectFieldSort"),
  sortType = document.querySelector("#sortType"),
  reverse = true;

sortType.addEventListener("click", () => {
  if (sortType.childNodes[1].classList.contains("fa-arrow-up-a-z")) {
    reverse = false;
    sortType.childNodes[1].classList.remove("fa-arrow-up-a-z");
    sortType.childNodes[1].classList.add("fa-arrow-down-z-a");
  } else {
    reverse = true;
    sortType.childNodes[1].classList.remove("fa-arrow-down-z-a");
    sortType.childNodes[1].classList.add("fa-arrow-up-a-z");
  }
});

btnSort.addEventListener("click", () => {
  let department = document.querySelector(".btn-check:checked").value;
  let reverseLocal = reverse ? "asc" : "dasc";
  let body = {
    type: "sort",
    department: department,
    field: selectFieldSort.value,
    order: reverseLocal,
  };

  let data = reponse(body);
  render(data);
});

// редактировать
let btnEdit = document.querySelector("#btnEdit"),
  inputIDEdit = document.querySelector("#inputIDEdit"),
  inputTimeStartEdit = document.querySelector("#inputTimeStartEdit"),
  inputTimeEndEdit = document.querySelector("#inputTimeEndEdit"),
  inputTypeEdit = document.querySelector("#inputTypeEdit"),
  btnOpenEdit = document.querySelector("#btnOpenEdit");

btnOpenEdit.addEventListener("mouseover", () => {
  if (document.querySelectorAll(".item-checkbox:checked").length > 1) {
    const modalErr = new bootstrap.Modal(
      document.getElementById("exampleModal")
    );
    modalErr.show();
  }
});

btnOpenEdit.addEventListener("click", () => {
  let id = document.querySelector(".item-checkbox:checked").value;
  inputValues = document.querySelectorAll(`#item${id}`);

  inputIDEdit.value = inputValues[0].textContent;
  inputTimeStartEdit.value = inputValues[1].textContent;
  inputTimeEndEdit.value = inputValues[2].textContent;
  inputTypeEdit.value = inputValues [3].textContent;
});


btnEdit.addEventListener("click", () => {
  let body = {
    type: "update",
    department: department,
    ID: String(inputIDEdit.value),
    obj: {
      ID: String(inputIDEdit.value),
      timeStart: inputTimeStartEdit.value,
      timeEnd: inputTimeEndEdit.value,
      type: inputTypeEdit.value,
    },
  };

  reponse(body);
});



// поиск
let btnSearch = document.querySelector("#btnSearch"),
  selectSearch = document.querySelector("#selectSearch"),
  inputSearch = document.querySelector("#inputSearch");

btnSearch.addEventListener("click", () => {

  let body = {
    type: "search",
    department: department,
    field: selectSearch.value,
    searchField:  inputSearch.value,
  };

  let data = reponse(body);

  render(data);
});

document.getElementById("checkAll").addEventListener("change", function () {
  const isChecked = this.checked;
  const checkboxes = document.querySelectorAll(".item-checkbox");

  checkboxes.forEach(function (checkbox) {
    checkbox.checked = isChecked;
  });
});

let xhr = new XMLHttpRequest();

function renderLesson(obj) {
  let keysDepartments = Object.keys(obj),
    valueDepartments = Object.values(obj);

  let titles = document.getElementById("infoAboutLessonTitle"),
    body = document.getElementById("infoAboutLessonBody");

  titles.innerHTML = "";
  body.innerHTML = "";

  for (let index in keysDepartments) {
    titles.innerHTML += `<h4 class="col ">${keysDepartments[index]}</h4>`;
    body.innerHTML += `
        <div class="col">
            <ul class="list-group list-group-flush">
              <li class="list-group-item bg-transparent text-info">ID: ${valueDepartments[index].lesson?.ID}</li>
  
              <li class="list-group-item bg-transparent text-info">
                Начало: ${valueDepartments[index].lesson?.timeStart}
              </li>
              <li class="list-group-item bg-transparent text-info">
                Конец: ${valueDepartments[index].lesson?.timeEnd}
              </li>
              <li class="list-group-item bg-transparent text-info">
                Тип: ${valueDepartments[index].lesson?.type}
              </li>
              <li class="list-group-item bg-transparent text-info">
                Осталось: ${valueDepartments[index].duration}
              </li>
            </ul>
      </div>`;
  }
}

function actualLesson() {
  xhr.open("POST", "https://neftpk.ru/schedule/server.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function update() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      let obj = JSON.parse(xhr.responseText);
      renderLesson(obj);
    }
  };

  xhr.send("action=getActuality");
}

setInterval(actualLesson, 1000);