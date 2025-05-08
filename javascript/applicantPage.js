// Define sample image paths (Ensure images exist in the "samples" folder)
const sampleImages = {
  "CTC-G11": "assets/samples/CTC-G11.jpg",
  "CTC-G12": "assets/samples/CTC-G12.jpg",
  "CTC-ID": "assets/samples/CTC-ID.jpg",
  "Birth-Certificate": "assets/samples/Birth-Certificate.jpg",
  "Applicant-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "Parent-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "ID-Picture": "assets/samples/ID-Picture.jpg",
};

function openSampleImage(documentType) {
  if (sampleImages[documentType]) {
    // Create modal elements
    const modal = document.createElement("div");
    modal.className =
      "popup fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex flex-col items-center flex-[1] z-50";

    // Create modal content
    modal.innerHTML = `
        <div class="relative w-full max-w-3xl">
          <img 
            src="${sampleImages[documentType]}" 
            alt="Sample ${documentType}" 
            class="max-w-full max-h-[70vh] object-contain rounded-lg"
          >
          <button 
            class="absolute -top-8 -right-8 bg-[rgb(145,_29,_52)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 px-4 py-2 hover:bg-[#0C5AAD]">
            Close
          </button>
        </div>
      `;

    // Add click handler to close button
    const closeButton = modal.querySelector("button");
    closeButton.addEventListener("click", () => {
      document.body.removeChild(modal);
      document.body.removeChild(overlay);
    });

    // Add dark overlay
    const overlay = document.createElement("div");
    overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-40";
    document.body.appendChild(overlay);

    // Add modal to body
    document.body.appendChild(modal);
  } else {
    alert("Sample image not available.");
  }
}
