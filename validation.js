/**
 * Главная функция валидации формы, вызывается при отправке.
 */
function formValidation() {
    // Получаем доступ к полям формы регистрации
    const login = document.forms["registrationForm"]["login"];
    const password = document.forms["registrationForm"]["password"];
    const address = document.forms["registrationForm"]["address"];
    const email = document.forms["registrationForm"]["email"];
    const phone = document.forms["registrationForm"]["phone"];

    // Последовательно вызываем функции валидации для каждого поля
    if (validateLogin(login)) {
        if (passid_validation(password, 5, 12)) {
            if (alphanumeric(address)) {
                if (ValidateEmail(email)) {
                    if (validatePhone(phone)) {
                        alert('Форма успешно прошла валидацию и будет отправлена на сервер.');
                        return true; // Все проверки пройдены
                    }
                }
            }
        }
    }

    return false; // Отмена отправки формы, если валидация не пройдена
}

/**
 * Проверяет, что в поле есть хотя бы одна буква.
 */
function validateLogin(field) {
    const letters = /[a-zA-Z]/; // Регулярное выражение для поиска любой буквы
    if (!field.value.match(letters)) {
        alert('Логин должен содержать хотя бы одну букву.');
        field.focus();
        return false;
    }
    return true;
}


/**

 * Проверяет длину пароля.
 * @param {HTMLInputElement} passid - Поле пароля
 * @param {number} mx - Минимальная длина
 * @param {number} my - Максимальная длина
 */
function passid_validation(passid, mx, my) {
    var passid_len = passid.value.length;
    if (passid_len == 0 || passid_len < mx || passid_len > my) {
        alert("Пароль не должен быть пустым и должен содержать от " + mx + " до " + my + " символов.");
        passid.focus();
        return false;
    }
    return true;
}

/**

 * Проверяет адрес на допустимые символы.
 * @param {HTMLInputElement} uadd - Поле адреса
 */
function alphanumeric(uadd) {
    // Поле адреса не является обязательным, поэтому если оно пустое, пропускаем проверку.
    if (uadd.value.length == 0) {
        return true;
    }
    var letters = /^[0-9a-zA-Zа-яА-ЯёЁ\s\.,-]+$/; // Допустимые символы: буквы, цифры, пробел, точка, запятая, дефис
    if (uadd.value.match(letters)) {
        return true;
    } else {
        alert('Адрес должен содержать только буквы, цифры и знаки препинания.');
        uadd.focus();
        return false;
    }
}

/**
 * Проверяет формат номера телефона.
 * @param {HTMLInputElement} uphone - Поле телефона
 */
function validatePhone(uphone) {
    // Поле телефона не является обязательным, поэтому если оно пустое, пропускаем проверку.
    if (uphone.value.length == 0) {
        return true;
    }
    // Допускает форматы: +7(XXX)XXX-XX-XX, 89XXXXXXXXX, +7 9XX XXX XX XX и т.д.
    var phoneFormat = /^(\+7|8)?[\s\-]?\(?(\d{3})\)?[\s\-]?(\d{3})[\s\-]?(\d{2})[\s\-]?(\d{2})$/;
    if (uphone.value.match(phoneFormat)) {
        return true;
    } else {
        alert("Вы ввели неверный формат номера телефона. Допустимый формат: +7(XXX)XXX-XX-XX или 89XXXXXXXXX.");
        uphone.focus();
        return false;
    }
}

/**

 * Проверяет формат email.
 * @param {HTMLInputElement} uemail - Поле email
 */
function ValidateEmail(uemail) {
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (uemail.value.match(mailformat)) {
        return true;
    } else {
        alert("Вы ввели неверный формат Email адреса!");
        uemail.focus();
        return false;
    }
}
