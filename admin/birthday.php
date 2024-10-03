<?php 
$str = file_get_contents('auth.txt');
$auth = json_decode($str, true);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(!($auth['login'] == $_POST['login'] && password_verify($_POST['password'], $auth['password']))){
    header('Location: /admin/index.html');
    die();
  }
}
setcookie('authorization', true, 0, "/");

if(empty($_COOKIE['authorization'])){
  header('Location: /admin/index.html');
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.css" />
    <title>birthday</title>
</head>
<body>
<body class="bg-dark text-white p-3 fs-5">
    <ul class="nav nav-tabs">
      <li class="nav-item bg-dark">
        <a class="nav-link" href="birthday.php">Дни рождения</a>
      </li>
      <li class="nav-item bg-dark">
        <a class="nav-link" href="schedule.php">Расписание</a>
      </li>
    </ul>

    <nav class="navbar">
      <div class="nav-item col d-flex gap-1">
        <select class="form-select col" id="selectSearch">
          <option value="ID">ID</option>
          <option value="Name">Name</option>
          <option value="Date">Date</option>
        </select>
        <input class="col" type="text" placeholder="Поиск" id="inputSearch" />
        <button
          type="button"
          class="col-md-auto btn btn-outline-info"
          id="btnSearch"
        >
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </div>
      <div class="nav-item col gap-1 d-flex justify-content-end">
        <button type="button" class="btn btn-outline-warning" id="refresh">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
        <button
          type="button"
          class="btn btn-outline-success"
          data-bs-toggle="modal"
          data-bs-target="#modalAdd"
        >
          <i class="fa-solid fa-plus"></i>
        </button>
        <button
          type="button"
          class="col-md-auto btn btn-outline-primary"
          data-bs-toggle="modal"
          id="btnOpenEdit"
          data-bs-target="#modalEdit"
        >
          <i class="fa-solid fa-pencil"></i>
        </button>
        <button
          type="button"
          class="col-md-auto btn btn-outline-light"
          data-bs-toggle="modal"
          data-bs-target="#modalSort"
        >
          <i class="fa-solid fa-filter"></i>
        </button>
        <button
          type="button"
          class="col-md-auto btn btn-outline-danger"
          data-bs-toggle="modal"
          data-bs-target="#modalDelete"
        >
          <i class="fa-solid fa-trash"></i>
        </button>
        <button
          type="button"
          id="btnRecovery"
          class="col-md-auto btn btn-outline-secondary"
        >
        <i class="fa-solid fa-hammer"></i>
        </button>
      </div>
    </nav>

    <div class="row">
      <p class="col-1 mb-0">ID</p>
      <p class="col mb-0">Name</p>
      <p class="col mb-0">Date</p>
    </div>

    <div id="list"></div>

    <!-- Модальное окно удаление  -->
    <div
      class="modal fade"
      id="modalDelete"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title text-danger">Удаление элемента</h5>
            <button
              type="button"
              class="btn-close bg-danger"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p>Все выделенные элементы будут удалены</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-outline-danger"
              data-bs-dismiss="modal"
            >
              Закрыть
            </button>
            <button
              type="submit"
              class="btn btn-outline-success"
              data-bs-dismiss="modal"
              id="btnDelete"
            >
              Применить
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно добавление -->
    <div
      class="modal fade"
      id="modalAdd"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title text-success" id="exampleModalLabel">
              Добавление элемента
            </h5>
            <button
              type="button"
              class="btn-close bg-danger"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="input-group mb-3">
              <span class="input-group-text">Name</span>
              <input type="text" class="form-control" id="inputNameAdd" />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">Date</span>
              <input type="text" class="form-control" id="inputDateAdd" />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-outline-danger"
              data-bs-dismiss="modal"
            >
              Закрыть
            </button>
            <button
              type="button"
              class="btn btn-outline-success"
              id="btnAdd"
              data-bs-dismiss="modal"
            >
              Применить
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Модальное окно сортировки -->
    <div
      class="modal fade"
      id="modalSort"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title text-ligth" id="exampleModalLabel">
              Сортировка элементов
            </h5>
            <button
              type="button"
              class="btn-close bg-danger"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="input-group">
              <select class="form-select" id="selectFieldSort">
                <option value="ID" selected>ID</option>
                <option value="Name">Name</option>
                <option value="Date">Date</option>
              </select>
              <button class="btn btn-outline-light" id="sortType">
                <i class="fa-solid fa-arrow-up-a-z"></i>
              </button>
            </div>

            <div class="form-check mt-1" id="radio">
              <input
              value="Year"
                class="form-check-input radio"
                type="radio"
                name="flexRadioDefault"
                id="flexRadioDefault1"
              />
              <label class="form-check-label" for="flexRadioDefault1">
                Год
              </label>
            </div>
            <div class="form-check" id="radio">
              <input
              value="Month"
                class="form-check-input radio"
                type="radio"
                name="flexRadioDefault"
                id="flexRadioDefault2"
                checked
              />
              <label class="form-check-label" for="flexRadioDefault2">
                Месяц
              </label>
            </div>
            <div class="form-check" id="radio">
              <input
                class="form-check-input radio"
                value="Day"
                type="radio"
                name="flexRadioDefault"
                id="flexRadioDefault2"
                checked
              />
              <label class="form-check-label" for="flexRadioDefault2">
                День
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-outline-danger"
              data-bs-dismiss="modal"
            >
              Закрыть
            </button>
            <button
              type="button"
              class="btn btn-outline-success"
              id="btnSort"
              data-bs-dismiss="modal"
            >
              Применить
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Модальное окно редактирования -->
    <div
      class="modal fade"
      id="modalEdit"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark">
          <div class="modal-header">
            <h5 class="modal-title text-primary" id="exampleModalLabel">
              Изменение элемента
            </h5>
            <button
              type="button"
              class="btn-close bg-danger"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <p>ID: <span id="inputIDEdit"></span></p>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">Name</span>
              <input
                id="inputNameEdit"
                type="text"
                class="form-control"
                aria-describedby="basic-addon1"
              />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">Date</span>
              <input
                id="inputDateEdit"
                type="text"
                class="form-control"
                aria-describedby="basic-addon1"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-outline-danger"
              data-bs-dismiss="modal"
            >
              Закрыть
            </button>
            <button
              type="button"
              class="btn btn-outline-success"
              id="btnEdit"
              data-bs-dismiss="modal"
            >
              Применить
            </button>
          </div>
        </div>
      </div>
    </div>
</body>

  <style>
      * {
        scroll-behavior: smooth;
        scrollbar-width: 1px;
        scrollbar-color: #007bff #212529;
      }

      body {
        width: 100vw;
        height: 100vh;
        overflow: hidden;
      }

      .tab-content {
        display: none;
      }
      .tab-content.active {
        display: block;
      }

      #list {
        max-height: 75%;
        overflow-y: auto;
        overflow-x: hidden;
      }

      #radio{
        display: none;
      }

      #radio.active{
        display: block;
      }
  </style>
  <script src="./bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="birthday.js"></script>
</html>