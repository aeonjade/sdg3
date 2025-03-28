const uploadedFiles = {};
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

const allowedFormats = {
  "CTC-G11": [".pdf"],
  "CTC-G12": [".pdf"],
  "CTC-ID": [".pdf"],
  "Birth-Certificate": [".pdf"],
  "Applicant-Voter-Certificate": [".pdf"],
  "Parent-Voter-Certificate": [".pdf"],
  "ID-Picture": [".jpg", ".png"],
};

function triggerUpload(id) {
  document.getElementById(id).click();
}



function removeFile(id) {
  const input = document.getElementById(id);
  const preview = document.getElementById("preview-" + id);
  const container = input.closest(".upload-container");
  const uploadBtn = container.querySelector(".upload-btn");
  const errorMessage = container.querySelector(".error-message");

  input.value = "";
  preview.classList.add("hidden");

  if (uploadBtn) uploadBtn.style.display = "block";
  if (errorMessage) errorMessage.classList.add("hidden");

  const checklistIcon = document.querySelector(`#item-${id} .info`);
  const checkIcon = document.querySelector(`#item-${id} .check`);
  if (checklistIcon && checkIcon) {
    checklistIcon.style.display = "inline";
    checkIcon.style.display = "none";
  }

  delete uploadedFiles[id];
  checkAllFilesUploaded();
}

function checkAllFilesUploaded() {
  const checkIcons = document.querySelectorAll(".check");
  const submitBtn = document.querySelector(".submit-btn");

  const allUploaded = Array.from(checkIcons).every(
    (icon) => icon.style.display === "inline"
  );
  if (submitBtn) submitBtn.disabled = !allUploaded;
}

function isValidFile(id, file) {
  const extension = file.name.split(".").pop().toLowerCase();
  return allowedFormats[id]?.includes("." + extension);
}

function toggleChecklist() {
  const checklistBox = document.querySelector(".checklist-box");
  const chevronIcon = document.querySelector(".chevron-icon");

  checklistBox.classList.toggle("minimized");
  if (chevronIcon) {
    chevronIcon.style.transform = checklistBox.classList.contains("minimized")
      ? "rotate(180deg)"
      : "rotate(0deg)";
  }
}

document.querySelectorAll(".file-input").forEach((input) => {
  input.addEventListener("change", function () {
    const id = this.id;
    const file = this.files[0];
    const preview = document.getElementById("preview-" + id);
    const container = input.closest(".upload-container");
    const uploadBtn = container.querySelector(".upload-btn");
    const errorMessage = container.querySelector(".error-message");

    if (errorMessage) {
      errorMessage.textContent = "";
      errorMessage.classList.remove("visible");
    }

    if (!file) return;

    if (!isValidFile(id, file)) {
      if (errorMessage) {
        errorMessage.textContent = `Invalid format. Allowed: ${allowedFormats[id].join(", ")}`;
        errorMessage.style.display = "block";
      }
      return;
    }

    if (file.size > MAX_FILE_SIZE) {
      if (errorMessage) {
        errorMessage.textContent = `File size exceeds ${MAX_FILE_SIZE / 1024 / 1024} MB.`;
        errorMessage.style.display = "block";
      }
      return;
    }

    // Display filename
    preview.querySelector(".file-name").textContent = file.name;
    preview.classList.remove("hidden");
    if (uploadBtn) uploadBtn.style.display = "none";

    // Checklist icon update
    const checklistIcon = document.querySelector(`#item-${id} .info`);
    const checkIcon = document.querySelector(`#item-${id} .check`);
    if (checklistIcon && checkIcon) {
      checklistIcon.style.display = "none";
      checkIcon.style.display = "inline";
    }

    // Save file
    uploadedFiles[id] = file;

    // Download icon
    const downloadIcon = preview.querySelector(".document-requirements-icon");
    if (downloadIcon) {
      downloadIcon.onclick = () => {
        const url = URL.createObjectURL(file);
        const link = document.createElement("a");
        link.href = url;
        link.download = file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      };
    }

    // View text
    const viewText = preview.querySelector(".view-text");
    if (viewText) {
      viewText.onclick = () => {
        const url = URL.createObjectURL(file);
        window.open(url, "_blank");
      };
    }

    checkAllFilesUploaded();
  });
});
