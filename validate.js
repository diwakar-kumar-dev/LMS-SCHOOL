// assets/validate.js
const onlyLetters = /^[A-Za-z]+$/;
const tenDigits   = /^\d{10}$/;
const emailRx     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function mark(el, ok){
  if(!el) return;
  el.classList.toggle('invalid', !ok);
}

function validateSignup(){
  const f = document.getElementById('firstname');
  const m = document.getElementById('middlename');
  const l = document.getElementById('lastname');
  const g = document.getElementById('gender');
  const mob = document.getElementById('mobile');
  const email = document.getElementById('email');
  const user = document.getElementById('username');
  const pass = document.getElementById('password');
  const cpass = document.getElementById('confirm_password');

  let ok = true;

  mark(f, onlyLetters.test(f.value.trim()));                ok &= onlyLetters.test(f.value.trim());
  if(m.value.trim().length) { mark(m, onlyLetters.test(m.value.trim())); ok &= onlyLetters.test(m.value.trim()); }
  mark(l, onlyLetters.test(l.value.trim()));                ok &= onlyLetters.test(l.value.trim());
  mark(g, !!g.value);                                       ok &= !!g.value;
  mark(mob, tenDigits.test(mob.value.trim()));              ok &= tenDigits.test(mob.value.trim());
  mark(email, emailRx.test(email.value.trim()));            ok &= emailRx.test(email.value.trim());
  mark(user, user.value.trim().length >= 4);                ok &= user.value.trim().length >= 4;
  mark(pass, pass.value.length >= 6);                       ok &= pass.value.length >= 6;
  mark(cpass, cpass.value === pass.value && cpass.value.length >= 6);
  ok &= (cpass.value === pass.value && cpass.value.length >= 6);

  return !!ok;
}

function validateLogin(){
  const user = document.getElementById('login_user');
  const pass = document.getElementById('login_pass');
  let ok = true;
  mark(user, user.value.trim().length >= 3); ok &= user.value.trim().length >= 3;
  mark(pass, pass.value.length >= 6);        ok &= pass.value.length >= 6;
  return !!ok;
}

document.addEventListener('input', (e)=>{
  // live re-check on input
  const form = e.target.closest('form');
  if(!form) return;
  if(form.id === 'signupForm') validateSignup();
  if(form.id === 'loginForm') validateLogin();
});
