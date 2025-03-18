<?php
// event-form.php
?>
<style>
    /* Form Container Styles */
    #event-form {
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    /* Label Styling */
    #event-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    /* Input Fields Styling */
    #event-form input[type="text"],
    #event-form input[type="date"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        color: #555;
    }

    #event-form input:focus {
        border-color: #0073aa;
        outline: none;
        box-shadow: 0 0 3px #0073aa;
    }

    /* Submit Button Styling */
    #event-form button[type="submit"] {
        width: 100%;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #0073aa;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button {
        padding: 12px 25px;
        margin-top: 25px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #0073aa;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    #event-form button[type="submit"]:hover {
        background-color: #005f8d;
    }

    /* Feedback Section Styling */
    #event-feedback {
        margin-top: 20px;
        font-size: 14px;
        color: #333;
        text-align: center;
    }

    .table-head > tr > th {
        color: white;
    }

    .events-table {
        margin-top: 2rem;
    }

</style>


<table class="events-table" style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
<thead class="table-head" style="background-color: #0073aa;">
    <tr>
        <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Event Name</th>
        <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Event Date and Time</th>
        <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Location</th>
        <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Action</th>
    </tr>
</thead>

<tbody class="events-table-body">
  
</tbody>
 
</table>

<button class="add-new-event">Add New Event</button>

<form id="event-form" style="display: none;">
    <label for="event-name">Event Name:</label>
    <input type="text" id="event-name" name="name" placeholder="Enter event name" required />

    <label for="event-date">Event Date and Time:</label>
    <input type="datetime-local" id="event-date" name="date" required />
    <br>
    <br>

    <label for="event-location">Event Location:</label>
    <input type="text" id="event-location" name="location" placeholder="Enter event location" required />

    <?php wp_nonce_field('event_form_nonce', 'event_nonce'); ?>

   <!-- <label for="event-time">Event Time:</label>
    <input type="time" id="event-time" placeholder="Enter the time of Event">  -->

    <button class="add-btn" type="submit">Add</button>
    <button style="display: none;" class="edit-btn" type="button">Update</button>


</form>

<!-- Event Form Submission Feedback -->
<div id="event-feedback"></div>
