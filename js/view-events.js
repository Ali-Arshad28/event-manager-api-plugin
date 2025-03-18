document.addEventListener('DOMContentLoaded', function() {

let events_table = document.querySelector(".events-table-body");


fetch(eventManager.apiUrl + 'events',{
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': eventManager.nonce // Use the localized nonce for security
},
}) // api for the get request
    .then(response => response.json())
    .then(data => display_data(data));

  

  function display_data(events){

    events.forEach(event => {
    
      let row = document.createElement('tr'); 

      row.innerHTML = `<td style="padding: 8px; border-bottom: 1px solid #ddd;">${event['name']}</td>
      <td style="padding: 8px; border-bottom: 1px solid #ddd;">${event['date']}</td>
      <td style="padding: 8px; border-bottom: 1px solid #ddd;">${event['location']}</td>`

      events_table.append(row);
      

      
    });
  
  

  }



});





