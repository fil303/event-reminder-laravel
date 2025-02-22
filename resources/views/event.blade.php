<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reminder App</title>
    <link href="{{ asset("assets/daisyui/full.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("assets/tailwind/tailwind.min.css") }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset("assets/datatable/dataTables.dataTables.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/toastr/toastr.min.css") }}" />
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between">
            <h1 class="text-3xl font-bold mb-8">Event Reminder</h1>
            <div>
                <button id="onlineButton" class="hidden btn btn-success">Online</button>
                <button id="offlineButton" class="hidden btn btn-error">Offline</button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between">
                <h2 class="text-xl font-semibold mb-4">Event List</h2>
                <div>
                    <a id="eventCreateButton" href="#eventImportModal" class="bg-blue-500 text-white px-4 py-2 rounded">Import CSV</a>
                    <a id="eventCreateButton" href="#eventModal" class="bg-blue-500 text-white px-4 py-2 rounded">Create Event</a>
                </div>
            </div>
            <table id="eventTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Emails</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" role="dialog" id="eventModal">
        <div class="modal-box">
            <div class="modal-action">
                <div class="">
                    <h2 class="text-xl font-semibold mb-4">Create Event</h2>
                    <form id="eventForm" class="space-y-4">
                        <input type="text" id="eventName" placeholder="Event Name" class="w-full p-2 border rounded">
                        <input type="datetime-local" id="eventDate" class="w-full p-2 border rounded">
                        <textarea id="eventDescription" placeholder="Description" class="w-full p-2 border rounded"></textarea>
                        <input type="text" id="eventEmails" placeholder="Emails (comma separated)" class="w-full p-2 border rounded">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create Event</button>
                        <a href="#" onclick="eventFormReset()" class="bg-red-500 text-white px-4 py-3 rounded">Close</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" role="dialog" id="eventImportModal">
        <div class="modal-box">
            <div class="modal-action">
                <div class="">
                    <h2 class="text-xl font-semibold mb-4">Import Event</h2>
                    <form id="eventImportForm" class="space-y-4">
                        <input type="file" id="eventfile" class="w-full p-2 border rounded">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Import</button>
                        <a href="#" onclick="eventImportFormReset()" class="bg-red-500 text-white px-4 py-3 rounded">Close</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset("assets/jquery/jquery-3.7.0.min.js") }}"></script>
    <script src="{{ asset("assets/datatable/dataTables.js") }}"></script>
    <script src="{{ asset("assets/datatable/ellipsis.js") }}"></script>
    <script src="{{ asset("assets/toastr/toastr.min.js") }}"></script>
    @include("event-js")
</body>

</html>