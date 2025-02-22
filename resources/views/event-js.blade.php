<script>
function isOnline(){
    return navigator.onLine;
}

function generateEventId(prefix = "EVT") {
    return `${prefix}-${Date.now()}`;
}

const STORAGE_KEY = "eventReminders";
function loadEvents() {
    const events = localStorage.getItem(STORAGE_KEY);
    return events ? JSON.parse(events) : [];
}

function saveEvents(events) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(events));
}

let dataTable;
function initializeDataTable() {
    dataTable = $("#eventTable").DataTable({
        order: [[2, 'desc']],
        columnDefs: [{
            targets: 4,
            render: DataTable.render.ellipsis( 20, true )
        }],
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "date" },
            { data: "description" },
            { data: "emails" },
            { 
                data: "status",
                
                render: function (data) {
                    return data === 0 ? `Upcoming` : 'Completed' ;
                },
            },
            {
                data: null,
                render: function (data) {
                    return `
                        <button onclick="editEvent('${data.id}')" class="bg-yellow-500 text-white px-2 py-1 rounded mr-2">Edit</button>
                        <button onclick="deleteEvent('${data.id}')" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    `;
                },
            },
        ],
    });
}

function renderEvents() {
    const events = loadEvents();
    const now = new Date();

    const formattedEvents = events.map(event => ({
        ...event,
        date: new Date(event.date).toLocaleString(),
        status: new Date(event.date) > now ? 0 : 1,
    }));

    dataTable.clear().rows.add(formattedEvents).draw();
}

function eventFormReset(){
    document.getElementById("eventName").value = "";
    document.getElementById("eventDate").value = null;
    document.getElementById("eventDescription").value = "";
    document.getElementById("eventEmails").value = "";

    if(document.getElementById("eventID")){
        document.getElementById("eventID").remove();
    }
}
function eventImportFormReset(){
    document.getElementById("eventfile").value = "";
}

document.getElementById("eventForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const eventName = document.getElementById("eventName").value;
    const eventDate = document.getElementById("eventDate").value;
    const eventDescription = document.getElementById("eventDescription").value;
    const eventEmails = document.getElementById("eventEmails").value;

    if(
        !(
            eventName &&
            eventDate &&
            eventDescription &&
            eventEmails
        )
    ){
        toastr.error("Please Fill All Input Filed", "Missing");
        eventFormReset();
        document.getElementById("eventForm").reset();
        window.location.href = "#";
        return;
    }

    let   newId = { id: generateEventId() };

    if(document.getElementById("eventID")){
        newId = { id: document.getElementById("eventID").value }
    }

    let newEvent = {
        ...newId,
        name: eventName,
        date: eventDate,
        description: eventDescription,
        emails: eventEmails,
    };

    let events = loadEvents();
    if(
        document.getElementById("eventID") &&
        !isOnline()
    ){
        let editId = document.getElementById("eventID").value;
        events = events.filter(event => event.id !== editId);
        document.getElementById("eventID").remove();
    }

    if(isOnline()){
        saveOnServer(newEvent);
    }else{
        newEvent = {
            ...newEvent,
            offline: true,
        };
        if(newEvent.description && newEvent.emails)
        events.push(newEvent);
        saveEvents(events);
        renderEvents();
    }

    eventFormReset();
    document.getElementById("eventForm").reset();
    window.location.href = "#";
});

document.getElementById("eventImportForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const eventName = document.getElementById("eventfile").files[0];

    if (!eventName) {
        toastr.error("Please select a CSV file first", "Missing");
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(event) {
        const csvText = event.target.result;
        const rows = csvText.split('\n').map(row => row.split(','));
        console.log("rows", rows);
        if(rows.length > 0){
            for (let index = 1; index < rows.length; index++) {
                const event = rows[index];
                let   newId = { id: generateEventId() };
                let   emails= event.splice(3);
                emails      = emails.join(',');

                let newEvent = {
                    ...newId,
                    name: event[0],
                    date: event[1],
                    description: event[2],
                    emails: emails,
                };

                if(isOnline()){
                    saveOnServer(newEvent, false);
                }else{
                    let events = loadEvents();
                    newEvent = {
                        ...newEvent,
                        offline: true,
                    };
                    if(newEvent.description && newEvent.emails)
                    events.push(newEvent);
                    saveEvents(events);
                    renderEvents();
                }

                eventImportFormReset();
                document.getElementById("eventForm").reset();
                window.location.href = "#";
            }
        }

    };
    reader.readAsText(eventName);
});

function saveOnServer(event, sync = false){
    const url = "{{ route('event.store') }}";
    $.post(
        url,
        {...event, _token: '{{ csrf_token() }}'},
        (response)=>{
            console.log(response);
            if(response.status){
                if(!sync) toastr.success(response.message);
                let events = response.data;
                saveEvents(events)
                renderEvents();
            }else{
                if(!sync) toastr.error(response.message, 'Failed!')
            }
        }
    )
}

function deleteOnServer(id){
    const url = `{{ route('event.delete') }}/${id}`;
    $.get(
        url,
        (response)=>{
            console.log(response);
            if(response.status){
                toastr.success(response.message);
                let events = response.data;
                saveEvents(events)
                renderEvents();
            }else{
                toastr.error(response.message, 'Failed!')
            }
        }
    )
}

window.editEvent = (id) => {
    const events = loadEvents();
    const event = events.find(event => event.id === id);

    if (event) {
        document.getElementById("eventName").value = event.name;
        document.getElementById("eventDate").value = event.date;
        document.getElementById("eventDescription").value = event.description;
        document.getElementById("eventEmails").value = event.emails;

        let idElement = document.createElement("input");
        idElement.setAttribute("type", "hidden");
        idElement.setAttribute("id", "eventID");
        idElement.setAttribute("name", "eventID");
        idElement.value = event.id;
        document.getElementById("eventForm").appendChild(idElement);

        window.location.href = "#eventModal";
    }
};

window.deleteEvent = (id) => {
    if(confirm("Are you sure want to delete event!")){
        deleteOnServer(id);
        // const events = loadEvents().filter(event => event.id !== id);
        // saveEvents(events);
        // renderEvents();
    }
};

$(document).ready(function () {
    const events = @json($events ?? []);
    console.log(events);
    initializeDataTable();
    saveEvents(events);
    renderEvents();
});

setInterval(()=>{
    if(isOnline()){
        document.getElementById("onlineButton").classList.remove('hidden');
        document.getElementById("offlineButton").classList.add('hidden');
        // for chrome
        document.getElementById("onlineButton").style.display = "block"
        document.getElementById("offlineButton").style.display = "none"

        // const events = loadEvents();
        // const offlineEvents = events.filter(event => event.offline === true);
        // offlineEvents.forEach((event)=> saveOnServer(event) );
    }else{
        document.getElementById("onlineButton").classList.add('hidden');
        document.getElementById("offlineButton").classList.remove('hidden');
        // for chrome
        document.getElementById("onlineButton").style.display = "none"
        document.getElementById("offlineButton").style.display = "block"
    }
},500)

window.addEventListener('online', ()=> {
    document.getElementById("onlineButton").classList.remove('hidden');
    document.getElementById("offlineButton").classList.add('hidden');

    const events = loadEvents();
    const offlineEvents = events.filter(event => event.offline === true);
    offlineEvents.forEach((event)=> saveOnServer(event) );
});
window.addEventListener('offline', ()=> {
    document.getElementById("onlineButton").classList.add('hidden');
    document.getElementById("offlineButton").classList.remove('hidden');
    
});

</script>