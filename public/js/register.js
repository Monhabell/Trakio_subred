/* BOTONES */
let btn_next = document.getElementById("btn_next");
let btn_back = document.getElementById("btn_back");

/* SECCIONES */
let section_2 = document.getElementById("section-2");
let section_3 = document.getElementById("section-3");
let section_4 = document.getElementById("section-4");
let register_btn = document.getElementById("register_btn");
const container_fills = document.getElementById("container-fills");

/* FORMULARIO */
let form_reg = document.getElementById("form-reg");

/* ------------------------------------------------------------
---------------------------SLIDES------------------------------
-------------------------------------------------------------*/

function incorrectField(field) {
  field.style.borderColor = "red";
  setTimeout(() => {
    field.removeAttribute("style");
  }, 500);
  field.focus();
}

let slide = 1;

btn_next.addEventListener("click", function (e) {
  e.preventDefault();

  switch (slide) {
    case 1:
      if (form_reg.subnet.value == "") {
        incorrectField(form_reg.subnet);
        break;
      }
      
      if (form_reg.environment.value == "") {
        incorrectField(form_reg.environment);
        break;
      }
      
      container_fills.classList.add("desp-30-l");
      btn_back.removeAttribute("hidden");
      section_2.hidden = false;
      slide++;

      break;

    case 2:
      if (form_reg.role_id.value == '') {
        incorrectField(form_reg.role_id);
        break;
      }

      if (form_reg.email.value.length < 3 || form_reg.email.value.indexOf("@") == -1 || form_reg.email.value.indexOf(".") == -1) {
        incorrectField(form_reg.email);
        break;
      }

      container_fills.classList.add("desp-60-l");
      section_3.hidden = false;
      slide++;

      break;

    case 3:
      if (form_reg.name.value.length < 3) {
        incorrectField(form_reg.name);
        break;
      }

      if (form_reg.last_name.value.length < 3) {
        incorrectField(form_reg.last_name);
        break;
      }
        
      container_fills.classList.add("desp-90-l");
      btn_next.hidden = true;
      section_4.hidden = false;
      register_btn.hidden = false;

      if (form_reg.password.value.length < 8 || form_reg.password_confirmation.value.length < 8 || form_reg.password_confirmation.value != form_reg.password.value) {
        register_btn.setAttribute("disabled", "true");
      } else {
        register_btn.removeAttribute("disabled");
      }
      slide++;

      break;
  }
});

btn_back.addEventListener("click", function (e) {
  e.preventDefault();
  if (slide == 2) {
    let container_fills = document.getElementById("container-fills");
    container_fills.classList.remove("desp-30-l");
    btn_back.hidden = true;
    setTimeout(function () {
      section_2.hidden = true;
    }, 500);
  } else if (slide == 3) {
    let container_fills = document.getElementById("container-fills");
    container_fills.classList.remove("desp-60-l");
    setTimeout(function () {
      section_3.hidden = true;
    }, 500);
  } else if (slide == 4) {
    let container_fills = document.getElementById("container-fills");
    container_fills.classList.remove("desp-90-l");
    btn_next.hidden = false;
    register_btn.hidden = true;
    setTimeout(function () {
      section_4.hidden = true;
    }, 500);
  }
  slide--;
});

/* ------------------------------------------------------------
---------------------VALIDAR CONTRASEÑA------------------------
-------------------------------------------------------------*/

form_reg.password.addEventListener("keyup", function(e){
  e.preventDefault();
  let res_pass1 = document.getElementById("res-pass1");

  if (password.value.length < 8) {
    res_pass1.innerHTML = '<div class="badge bg-danger text-wrap" style="width: 100%;">La contraseña debe contener al menos 8 caracteres</div>';
    register_btn.setAttribute("disabled", "true");
  }else{
    res_pass1.innerHTML = "";
  }
});

form_reg.password_confirmation.addEventListener("keyup", function(e){
  e.preventDefault();
  let res_pass2 = document.getElementById("res-pass2");
  if(form_reg.password_confirmation.value.length >= 8){
    if (form_reg.password_confirmation.value == password.value) {
      res_pass2.innerHTML = "";
      register_btn.removeAttribute("disabled");
    } else {
      res_pass2.innerHTML = "Las contraseñas no coinciden";
      register_btn.setAttribute("disabled", "true");
    }
  }else {
    register_btn.setAttribute("disabled", "true");
  }
});