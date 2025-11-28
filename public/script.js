// Subtle glow animation on input focus
const inputs = document.querySelectorAll("input");

inputs.forEach(input => {
    input.addEventListener("focus", () => {
        document.body.style.backgroundColor = "#0b1225";
    });
    input.addEventListener("blur", () => {
        document.body.style.backgroundColor = "#0b1120";
    });
});
