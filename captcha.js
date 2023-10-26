// Generate a random math question and display it to the user
function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10);
    const num2 = Math.floor(Math.random() * 10);
    const operator = ['+', '-', '*'][Math.floor(Math.random() * 3)];
    const captchaQuestion = `${num1} ${operator} ${num2} = ?`;
    
    const captchaChallenge = document.getElementById('captchaChallenge');
    captchaChallenge.textContent = captchaQuestion;
}

// Verify the user's answer
function validateCaptcha() {
    const captchaInput = document.getElementById('captchaInput').value;
    const captchaChallenge = document.getElementById('captchaChallenge').textContent;

    const expectedAnswer = eval(captchaChallenge);

    if (parseInt(captchaInput) === expectedAnswer) {
        alert('Captcha solved! Form submitted.');
    } else {
        alert('Incorrect captcha. Please try again.');
    }

    generateCaptcha(); // Generate a new captcha after submission
}

// Initialize the captcha and form submission
document.addEventListener('DOMContentLoaded', function () {
    generateCaptcha();
    const captchaForm = document.getElementById('captchaForm');
    captchaForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally
        validateCaptcha();
    });
});
