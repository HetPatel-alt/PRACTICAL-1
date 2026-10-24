const form=document.getElementById("registration-form");
if(form){
  const formMessage=document.getElementById("form-message");
  const passwordInput=document.getElementById("password");
  const confirmPasswordInput=document.getElementById("confirm-password");
  const strengthBar=document.getElementById("strength-bar");
  const strengthText=document.getElementById("strength-text");
  const validators={
    fullName:{input:document.getElementById("full-name"),error:document.getElementById("full-name-error"),validate(v){return /^[A-Za-z][A-Za-z .'-]{1,49}$/.test(v.trim())?"":"Enter a name using letters and normal punctuation (2–50 characters).";}},
    email:{input:document.getElementById("email"),error:document.getElementById("email-error"),validate(v){return /^[A-Za-z0-9.!#$%&'*+/=?^_`{|}~-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+$/.test(v.trim())?"":"Enter a valid email address, such as student@example.com.";}},
    mobile:{input:document.getElementById("mobile"),error:document.getElementById("mobile-error"),validate(v){return /^(?:\+91[- ]?)?[6-9]\d{9}$/.test(v.trim())?"":"Enter a valid 10-digit Indian mobile number, optionally prefixed with +91.";}},
    course:{input:document.getElementById("course"),error:document.getElementById("course-error"),validate(v){return v?"":"Select a course.";}},
    year:{input:document.getElementById("year"),error:document.getElementById("year-error"),validate(v){return v?"":"Select your year of study.";}},
    password:{input:passwordInput,error:document.getElementById("password-error"),validate(v){return v.length>=8&&/[A-Z]/.test(v)&&/[a-z]/.test(v)&&/\d/.test(v)&&/[^A-Za-z0-9]/.test(v)?"":"Use at least 8 characters with uppercase, lowercase, a number, and a special character.";}},
    confirmPassword:{input:confirmPasswordInput,error:document.getElementById("confirm-password-error"),validate(v){if(!v)return"Confirm your password.";return v===passwordInput.value?"":"Passwords do not match.";}}
  };
  function showError(field,message){
    field.error.textContent=message;field.input.setAttribute("aria-invalid",String(Boolean(message)));
    return !message;
  }
  function validateField(name){const field=validators[name];return showError(field,field.validate(field.input.value));}
  function validateGender(){
    const selected=form.querySelector('input[name="gender"]:checked');
    document.getElementById("gender-error").textContent=selected?"":"Select a gender option.";
    return Boolean(selected);
  }
  function validateTerms(){
    const checkbox=document.getElementById("terms");const valid=checkbox.checked;
    document.getElementById("terms-error").textContent=valid?"":"You must accept the terms and conditions.";
    checkbox.setAttribute("aria-invalid",String(!valid));return valid;
  }
  function updatePasswordStrength(){
    const value=passwordInput.value;let score=0;
    if(value.length>=8)score++;if(/[A-Z]/.test(value))score++;if(/[a-z]/.test(value))score++;if(/\d/.test(value))score++;if(/[^A-Za-z0-9]/.test(value))score++;
    const labels=["Not entered","Very weak","Weak","Fair","Good","Strong"];
    const widths=["0%","20%","40%","60%","80%","100%"];
    const colors=["#e2e8ea","#b42318","#d97706","#ca8a04","#2978a0","#187443"];
    strengthBar.style.width=widths[score];strengthBar.style.backgroundColor=colors[score];
    strengthText.textContent=`Password strength: ${labels[score]}`;
  }
  Object.keys(validators).forEach(name=>{
    const field=validators[name];
    field.input.addEventListener("blur",()=>validateField(name));
    field.input.addEventListener("input",()=>{
      if(field.input.getAttribute("aria-invalid")==="true")validateField(name);
      if(name==="password"){updatePasswordStrength();if(confirmPasswordInput.value)validateField("confirmPassword");}
      if(name==="confirmPassword")validateField(name);
    });
    field.input.addEventListener("change",()=>validateField(name));
  });
  form.querySelectorAll('input[name="gender"]').forEach(radio=>radio.addEventListener("change",validateGender));
  document.getElementById("terms").addEventListener("change",validateTerms);
  form.addEventListener("submit",event=>{
    event.preventDefault();
    const validFields=Object.keys(validators).map(validateField).every(Boolean);
    const valid=validFields&&validateGender()&&validateTerms();
    if(valid){
      formMessage.textContent="Registration form validated successfully. No data has been sent to a server.";
      formMessage.className="form-message success";
    }else{
      formMessage.textContent="Please correct the highlighted fields and try again.";
      formMessage.className="form-message failure";
      const firstInvalid=form.querySelector('[aria-invalid="true"]');if(firstInvalid)firstInvalid.focus();
    }
  });
  form.addEventListener("reset",()=>window.setTimeout(()=>{
    Object.values(validators).forEach(field=>showError(field,""));
    document.getElementById("gender-error").textContent="";
    document.getElementById("terms-error").textContent="";
    document.getElementById("terms").removeAttribute("aria-invalid");
    formMessage.textContent="";formMessage.className="form-message";updatePasswordStrength();
  },0));
  updatePasswordStrength();
}
