


/* ==== ADD & Edit === */

function addIngredient() {
  const div = document.createElement("div");
  div.className = "row";
  div.innerHTML = `
    <input name="ingredient[]" type="text" placeholder="Enter ingredient" required>
    <input name="quan[]" type="text" placeholder="Enter quantity" required>
  `;
  document.getElementById("ingredients").appendChild(div);
}

let stepCounter = 1;

function addStep() {
  stepCounter++;

  const div = document.createElement("div");
  div.className = "row"; 
  div.innerHTML = ` <input name="instruction[]" type="text" placeholder="Step ${stepCounter}: Enter instructions" required> `;

  document.getElementById("steps").appendChild(div);
}

function EditgoToMyRecipes() {
  alert("Recipe updated successfully!");
  window.location.href = "my-recipes.html"; 
  return false; 
}

/* ====  END === ADD & Edit === */

window.onload = function() {
  document.querySelectorAll('.salad-img').forEach(img => {img.style.transform = "rotate(360deg)";
  });
};