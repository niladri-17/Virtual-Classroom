// Select elements
const themeToggle = document.getElementById("themeToggle");
const signinBtn = document.getElementById("signinBtn");
const signinModal = document.getElementById("signinModal");
const signupModal = document.getElementById("signupModal");
const forgotPasswordModal = document.getElementById("forgotPasswordModal");
const joinClassModal = document.getElementById("joinClassModal");
const editAnModal = document.getElementById("editAnModal");
const createClassModal = document.getElementById("createClassModal");
const closeButtons = document.querySelectorAll(".close-btn");
const showSignupLink = document.getElementById("showSignup");
const showSigninLink = document.getElementById("showSignin");
const forgotPasswordLink = document.getElementById("forgotPasswordLink");
const backToSignin = document.getElementById("backToSignin");
const dropdown = document.querySelector(".dropdown");
const plusIcon = document.getElementById("plusIcon");
const dropdownContent = document.getElementById("dropdownContent");
const createClass = document.getElementById("createClass");
const joinClass = document.getElementById("joinClass");
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
const enrolledList = document.getElementById("enrolled-list");
const enrolledClasses = document.getElementById("enrolled-classes");
const teachingList = document.getElementById("teaching-list");
const teachingClasses = document.getElementById("teaching-classes");
const logout = document.getElementById("logout");
const classCodeModal = document.getElementById("#classCodeModal");
const classQuill1 = document.getElementById("#classEditor1");
const postQuill1 = document.getElementById("#editor1");
const postQuill2 = document.getElementById("#editor2");

if (teachingList) {
  teachingList.addEventListener("click", () => {
    teachingClasses.classList.toggle("opened");
    const teachingDropdown = teachingList.querySelector("i.fa-caret-right");
    if (teachingClasses.classList.contains("opened")) {
      teachingClasses.style.height = `${teachingClasses.scrollHeight}px`;
      teachingDropdown.classList.add("rotated");
    } else {
      teachingClasses.style.height = "0";
      teachingDropdown.classList.remove("rotated");
    }
  });
}

if (enrolledList) {
  enrolledList.addEventListener("click", () => {
    enrolledClasses.classList.toggle("opened");
    const enrolledDropdown = enrolledList.querySelector("i.fa-caret-right");
    if (enrolledClasses.classList.contains("opened")) {
      enrolledClasses.style.height = `${enrolledClasses.scrollHeight}px`;
      enrolledDropdown.classList.add("rotated");
    } else {
      enrolledClasses.style.height = "0";
      enrolledDropdown.classList.remove("rotated");
    }
  });
}

function enrolledClassDropdownClose() {
  $("#enrolled-classes").css("height", "0px")
  $("#enrolled-classes").removeClass("opened")
  $("#enrolled-dropdown").removeClass("rotated")
}

function fetchEnrolledClasses() {
  $.ajax({
      type: "POST",
      url: "controllers/alike.php",
      data: {
          enrolled_list: 1,
      },
      success: function (response) {
          const res = jQuery.parseJSON(response)
          if (res.status == 1) {
              $("#enrolled-classes-list").html(res.message);
          }
      }
  });
}

function TeachingClassDropdownClose() {
  $("#teaching-classes").css("height", "0px")
  $("#teaching-classes").removeClass("opened")
  $("#teaching-dropdown").removeClass("rotated")
}

function fetchTeachingClasses() {
  $.ajax({
      type: "POST",
      url: "controllers/alike.php",
      data: {
        teaching_list: 1,
      },
      success: function (response) {
          const res = jQuery.parseJSON(response)
          if (res.status == 1) {
              $("#teaching-classes-list").html(res.message);
          }
      }
  });
}

// Event listeners
themeToggle.addEventListener("click", toggleTheme);
if (signinBtn) {
  signinBtn.addEventListener("click", () => openModal(signinModal));
  showSignupLink.addEventListener("click", () =>
    toggleModal(signinModal, signupModal)
  );
  showSigninLink.addEventListener("click", () =>
    toggleModal(signupModal, signinModal)
  );
  backToSignin.addEventListener("click", () =>
    toggleModal(forgotPasswordModal, signinModal)
  );
  forgotPasswordLink.addEventListener("click", () =>
    toggleModal(signinModal, forgotPasswordModal)
  );
}
if (closeButtons)
  closeButtons.forEach((btn) => btn.addEventListener("click", closeModal));
if (plusIcon) {
  plusIcon.addEventListener("click", toggleDropdown);
  createClass.addEventListener("click", () => openModal(createClassModal));
  joinClass.addEventListener("click", () => openModal(joinClassModal));
}
window.addEventListener("click", outsideClick);

if (hamburger) hamburger.addEventListener("click", toggleSidebar);

const currentTheme = localStorage.getItem("currentTheme");
if (currentTheme) {
  document.body.classList.add("dark-theme");
  themeToggle.classList.add("fa-sun");
  themeToggle.classList.remove("fa-moon");
}
// Functions
function toggleTheme() {
  document.body.classList.toggle("dark-theme");
  themeToggle.classList.toggle("fa-sun");
  themeToggle.classList.toggle("fa-moon");
  if (document.body.classList.contains("dark-theme")) {
    localStorage.setItem("currentTheme", "dark");
  } else {
    localStorage.removeItem("currentTheme");
  }
}

function openModal(modal) {
  modal.style.display = "block";
}

function closeModal() {
  this.closest(".modal").style.display = "none";
}

function toggleModal(currentModal, targetModal) {
  currentModal.style.display = "none";
  targetModal.style.display = "block";
}

function toggleDropdown() {
  dropdownContent.classList.toggle("show");
}

function toggleSidebar() {
  sidebar.classList.toggle("closed");
  if (sidebar.classList.contains("closed")) {
    document.querySelector("main").style.width = "100vw";
    document.querySelector(".content").style.width = "100vw";
  } else {
    document.querySelector("main").style.removeProperty("width");
    document.querySelector(".content").style.removeProperty("width");
  }
}

function outsideClick(e) {
  if (e.target == signinModal) {
    signinModal.style.display = "none";
  }
  if (e.target == signupModal) {
    signupModal.style.display = "none";
  }
  if (e.target == forgotPasswordModal) {
    forgotPasswordModal.style.display = "none";
  }
  if (e.target == joinClassModal) {
    joinClassModal.style.display = "none";
  }
  if (e.target == createClassModal) {
    createClassModal.style.display = "none";
  }
  if (classCodeModal) {
    if (e.target == classCodeModal) {
      classCodeModal.style.display = "none";
    }
  }
  if (editAnModal) {
    if (e.target == editAnModal) {
      editAnModal.style.display = "none";
    }
  }

  if (dropdown) {
    if (!dropdown.contains(e.target)) {
      dropdownContent.classList.remove("show");
    }
  }
}

const notifier = new AWN({
  durations: {
    global: 5000, // Default duration for all notifications (in milliseconds)
    success: 2000, // Duration for success notifications
    alert: 3000, // Duration for alert notifications
    warning: 2000, // Duration for warning notifications
    info: 3500, // Duration for info notifications
  },
});

function logoutAction(callback) {
  notifier.confirm(
    "You will be logged out.",
    () => {
      callback(true);
    },
    () => {
      callback(false);
    },
    {
      labels: {
        confirm: "Logout?",
        confirmOk: "OK",
        confirmCancel: "Cancel",
      },
    }
  );
}

logout.onclick = () => {
  logoutAction(function (status) {
    console.log(status); // This logs the user's response (true or false)
    if (status) {
      window.location.href = "logout.php";
    }
  });
};

// if (postQuill1 || postQuill2) {
// const quill2 = new Quill("#Editor1", {
//   modules: {
//     toolbar: [["bold", "italic", "underline"], [{ list: "bullet" }]],
//   },
//   placeholder: "Add a comment...",
//   theme: "snow", // or 'bubble'
// });
// const quill3 = new Quill("#Editor2", {
//   modules: {
//     toolbar: [["bold", "italic", "underline"], [{ list: "bullet" }]],
//   },
//   placeholder: "Add a comment...",
//   theme: "snow", // or 'bubble'
// });
// }
