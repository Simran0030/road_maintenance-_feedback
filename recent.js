document.addEventListener("DOMContentLoaded", function () {
  fetchRecentFeedback();
});

function fetchRecentFeedback() {
  // Fetch recent feedback from the server using fetch API
  fetch("fetch_recent_feedback.php")
    .then((response) => response.json())
    .then((data) => {
      const feedbackList = document.getElementById("feedback-list");
      feedbackList.innerHTML = ""; // Clear previous content

      // Check if feedback data exists
      if (data.length > 0) {
        data.forEach((feedback) => {
          // Create a div element to hold the feedback entry
          const feedbackDiv = document.createElement("div");
          feedbackDiv.className = "feedback-entry";

          // Format the feedback data with the image
          feedbackDiv.innerHTML = `
                            <h3>${feedback.name}</h3>
                            <p><strong>City:</strong> ${feedback.city}</p>
                            <p><strong>State:</strong> ${feedback.state}</p>
                            <p><strong>Zip:</strong> ${feedback.zip}</p>
                            <p><strong>Location:</strong> ${feedback.location}</p>
                            <p><strong>Issue:</strong> ${feedback.feedback}</p>
                            <p><strong>Urgency:</strong> ${feedback.urgency}</p>
                            ${feedback.Location_Image ? `<img src="${feedback.Location_Image}" alt="Road Issue Image" style="max-width: 200px; height: auto;"/>` : '<p>No image available.</p>'}
                        `;

          // Append the feedback entry to the feedback list
          feedbackList.appendChild(feedbackDiv);
        });
      } else {
        feedbackList.innerHTML = "<p>No feedback available.</p>";
      }
    })
    .catch((error) => console.error("Error fetching recent feedback:", error));
}
