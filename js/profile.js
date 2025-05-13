function uploadImage() {
  const fileInput = document.getElementById('fileInput');
  const profilePic = document.getElementById('profilePic');

  const file = fileInput.files[0];
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          profilePic.src = e.target.result;
      };
      reader.readAsDataURL(file);
  }
}

// Store the current URL as the "previous page" unless the user is already on the profile page
document.addEventListener("DOMContentLoaded", function () {
  if (!window.location.href.includes("profile")) {
      sessionStorage.setItem("previousPage", window.location.href);
  }
});

function goBack() {
  const previousPage = sessionStorage.getItem("previousPage");
  if (previousPage) {
      window.location.href = previousPage; // Navigate to the stored previous page
  } else {
      window.location.href = "userdashboard.php"; // Fallback to the dashboard
  }
}

function logout() {
  window.location.href = "login.php";
}



