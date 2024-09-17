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
    <p class="col-md-auto mb-0"><input class="form-check-input item-checkbox" type="checkbox" value="${item.ID}"></p>
    <p class="col-1 mb-0" id="item${item.ID}">${item.ID}</p>
    <p class="col mb-0" id="item${item.ID}">${item.Name}</p>
    <p class="col mb-0" id="item${item.ID}">${item.Date}</p>
  </div>
  `;
    }
  });
}

// удаление
let btnDelete = document.querySelector("#btnDelete"),
  inputDelete = document.querySelector("#inputDelete");

btnDelete.addEventListener("click", () => {
  let temp = [];
  document.querySelectorAll(".item-checkbox:checked").forEach((item) => {
    temp.push(item.value);
  });
  

  if (Array.isArray(temp) && temp.length !== 0) {
    for (let item of temp) {
      let body = {
        type: "delete",
        ID: item,
      };
      reponse(body);
    }
    refresh();
  }

  let body = {
    type: "delete",
    ID: inputDelete.value,
  };
  reponse(body)
});

// добавить
let btnAdd = document.querySelector("#btnAdd"),
  inputNameAdd = document.querySelector("#inputNameAdd"),
  inputDateAdd = document.querySelector("#inputDateAdd");

btnAdd.addEventListener("click", () => {
  let body = {
    type: "add",
    obj: {
      Name: inputNameAdd.value,
      Date: inputDateAdd.value,
    },
  };

  reponse(body);
});

// сортировать
let btnSort = document.querySelector("#btnSort"),
  selectFieldSort = document.querySelector("#selectFieldSort"),
  sortType = document.querySelector("#sortType"),
  reverse = true;

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
  let reverseLocal = reverse ? "asc" : "dasc";
  let date = document.querySelector(".radio:checked").value;
  let body = {
    type: "sort",
    field: selectFieldSort.value,
    order: reverseLocal,
    date: date
  };

  let data = reponse(body);
  render(data);
});

// редактировать
let btnEdit = document.querySelector("#btnEdit"),
  inputIDEdit = document.querySelector("#inputIDEdit"),
  inputNameEdit = document.querySelector("#inputNameEdit"),
  inputDateEdit = document.querySelector("#inputDateEdit"),
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
  inputNameEdit.value = inputValues[1].textContent;
  inputDateEdit.value = inputValues[2].textContent;
});

btnEdit.addEventListener("click", () => {
  let body = {
    type: "update",
    ID: String(inputIDEdit.value),
    obj: {
      ID: String(inputIDEdit.value),
      Name: inputNameEdit.value,
      Date: inputDateEdit.value,
    },
  };

  reponse(body);
});

// обновить
function refresh(){
  let body = {
    type: "get",
  };

  let data = reponse(body);
  render(data);
}

let btnRefresh = document.querySelector("#refresh");
btnRefresh.addEventListener("click", refresh);

// поиск
let btnSearch = document.querySelector("#btnSearch"),
  selectSearch = document.querySelector("#selectSearch"),
  inputSearch = document.querySelector("#inputSearch");

btnSearch.addEventListener("click", () => {
  let body = {
    type: "search",
    field: selectSearch.value,
    searchField: inputSearch.value,
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

refresh();

