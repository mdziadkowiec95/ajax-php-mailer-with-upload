const form = document.querySelector('.form');
const formResponseMessage = document.querySelector('.form-response-message');
const loader = document.querySelector('.lds-ring');

function sendRequest(data) {
  form.classList.add('in-progress');
  loader.classList.add('active');

  fetch("process.php", {
    method: "post",
    body: data
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        form.classList.remove('in-progress');
        loader.classList.remove('active');
        formResponseMessage.className = 'form-response-message visible success';
        formResponseMessage.textContent = data.msg;

        setTimeout(() => {
          formResponseMessage.className = 'form-response-message';
        }, 5000);


      } else {
        form.classList.remove('in-progress');
        loader.classList.remove('active');
        formResponseMessage.className = 'form-response-message visible error';
        formResponseMessage.textContent = data.msg;
      }
    })
    .catch(err => console.log(err))
}


function handleFormSubmit(e) {
  e.preventDefault();

  const fullName = document.querySelector('input[name=fullname]'),
    phone = document.querySelector('input[name=phone'),
    email = document.querySelector('input[name=email]'),
    damage = document.querySelector('input[name=damage]:checked'),
    file = document.querySelector('input[name=userfile]').files[0];

    const formData = new FormData();
    formData.append('fullName', fullName.value);
    formData.append('email', email.value);
    formData.append('phone', phone.value);
    formData.append('damage', damage.value);
    formData.append('userFile', file);

    sendRequest(formData)
  

}

document.querySelector('.form').addEventListener('submit', handleFormSubmit);