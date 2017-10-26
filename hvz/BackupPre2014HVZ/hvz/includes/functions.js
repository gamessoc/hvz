
/*
Library of useful Javascript functions
*/

// Call from a form's onsubmit. Checks if the form field "submit_ok" is true,
// and allows the submission to continue if so, or stop it if not.
function checkSubmit( form ) {
  if( form.submit_ok.value != 'false' && form.submit_ok.value != '' ) {
    return true;
  }
  else {
    return false;
  }
}

// Given a control, sets the given field of the control's form to the given
// val.
function setFormValue( control, field, val ) {
  var f = control.form;
  
  for( var i = 0; i < f.length; i++ ) {
    if( f.elements[i].name == field ) {
      f.elements[i].value = val;
    }
  }
  
  return val;
}
