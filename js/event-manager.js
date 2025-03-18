// event-manager.js

document.addEventListener('DOMContentLoaded', function() {

    let event_form = document.getElementById('event-form');

  // Handle form submission to Add the new event
  event_form.addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent default form submission

      // Get the form data
      var eventData = {
          name: document.getElementById('event-name').value,
          date: document.getElementById('event-date').value,
          location: document.getElementById('event-location').value
      };

      console.log(eventData);  // Debugging output

      // Send the data using the REST API
      fetch(eventManager.apiUrl + 'events', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-WP-Nonce': eventManager.nonce // Use the localized nonce for security
          },
          body: JSON.stringify(eventData)
      })
      .then(response => response.json())
      .then(data => {
          let feedback = document.getElementById('event-feedback');
          if (data.status === 'success') {
              feedback.innerHTML = '<p style="color: green;">Event Created Successfully!</p>';
          } else {
              feedback.innerHTML = `<p style="color: red;">Error: ${data.message}</p>`;
          }
      })
      .catch(error => {
          let feedback = document.getElementById('event-feedback');
          feedback.innerHTML = `<p style="color: red;">Error: ${error.message || error}</p>`;
      });

      location.reload();
  });


  let events_table = document.querySelector(".events-table-body");

  fetch(eventManager.apiUrl + 'events',{
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': eventManager.nonce // Use the localized nonce for security
  },
  }) // api for the get request
      .then(response => response.json())
      .then(data => display_data(data));
  
    
  // for showing events data in admin page
    function display_data(events){
  
      events.forEach(event => {
      
        let row = document.createElement('tr'); 
  
        row.innerHTML = `<td class="event-name" style="padding: 8px; border-bottom: 1px solid #ddd;">${event['name']}</td>
        <td class="event-date" style="padding: 8px; border-bottom: 1px solid #ddd;">${event['date']}</td>
        <td class="event-location" style="padding: 8px; border-bottom: 1px solid #ddd;">${event['location']}</td>
        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><button class="updt-btn" data-id=${event['id']} style="padding: 8px; border: 1px solid white;">Edit</button>
        <button class="rmv-btn" data-id=${event['id']} style="padding: 8px; border: 1px solid white;">Remove</button>
        </td>
        `
  
        events_table.append(row);
        
  
        
      });
    
    
  
    }

 // To remove the record from the database with confirmation
events_table.addEventListener('click', (event) => {
    // Check if the clicked element is a remove button
    if (event.target.classList.contains('rmv-btn')) {
        let id = event.target.dataset.id; // Access data-id from the clicked button

        // Show confirmation dialog
        let confirmRemove = confirm("Are you sure you want to remove this event?");
        if (!confirmRemove) {
            return; // Cancel action if the user selects "Cancel"
        }

        console.log("Remove button clicked and confirmed for removal");

        // Proceed with delete operation
        fetch(`${eventManager.apiUrl}events/${id}`, { // Use template literals for URLs
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': eventManager.nonce, // Use the localized nonce for security
            },
        })
        .then(response => response.json())
        .then(data => {
            let feedback = document.getElementById('event-feedback');
            if (data.status === 'success') {
                alert("Event Deleted Successfully");
                event.target.closest('tr').remove(); // Remove the event row from the table
            } else {
                feedback.innerHTML = `<p style="color: red;">Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            let feedback = document.getElementById('event-feedback');
            feedback.innerHTML = `<p style="color: red;">Error: ${error.message || error}</p>`;
        });
    }
});


// function to update the event when the edit button clicks

  events_table.addEventListener('click', (event) => {
    // Check if the clicked element is a remove button
    if (event.target.classList.contains('updt-btn')) {
        let id = event.target.dataset.id; // Access data-id from the clicked button
        event_form.style.display ="block";

        let row = event.target.closest('tr');
        event_form.querySelector('#event-name').value = row.querySelector(".event-name").textContent;
        event_form.querySelector('#event-date').value = row.querySelector(".event-date").textContent;
        event_form.querySelector('#event-location').value = row.querySelector(".event-location").textContent;

        event_form.querySelector(".add-btn").style.display= "none";
        event_form.querySelector(".edit-btn").style.display= "block";


    event_form.querySelector('.edit-btn').addEventListener('click', ()=>{

    // Get the form data
      var eventData = {
        name: document.getElementById('event-name').value,
        date: document.getElementById('event-date').value,
        location: document.getElementById('event-location').value
    };


        // Proceed with delete operation
        fetch(`${eventManager.apiUrl}events/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': eventManager.nonce // Use the localized nonce for security
            },
            body: JSON.stringify(eventData)
        })
        .then(response => response.json())
        .then(data => {
            let feedback = document.getElementById('event-feedback');
            if (data.status === 'success') {
                location.reload();     
                alert("Event Updated Successfully");

                } 
                else {
                feedback.innerHTML = `<p style="color: red;">Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            let feedback = document.getElementById('event-feedback');
            feedback.innerHTML = `<p style="color: red;">Error: ${error.message || error}</p>`;
        });
    });
    }
  });

    // To display the add event form on button click
    document.querySelector(".add-new-event").addEventListener('click', ()=> {

        event_form.style.display ="block";

    })
    



});
