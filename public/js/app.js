let freeHours = [];
let takenIntervals = [];
let selectedDate = "";

fetch("http://localhost/pabau/api/services.php")
.then(res => res.json())
.then(data => {
    $("#services").append(data.data.map(item => `<option value="${item.id}">${item.name}</option>`));
})



$ ('#datepicker').datepicker ({
  minDate: 0,
  dateFormat: "dd/mm/yy",
  beforeShowDay: function (date) {
    var day = date.getDay ();
    return [day == 0 || day == 6 ? false : true, ''];
  },
  onSelect: function () {
    let date = $ (this).datepicker ('getDate');
    var formData = new FormData();
    selectedDate =  $.datepicker.formatDate('yy-mm-dd', date)

    formData.append('day',  selectedDate);
    fetch("http://localhost/pabau/api/isFree.php", {
        method: "POST",
        body: formData
    }).then(res => res.json()).then(data => {
        $("#start_time").html('<option value="" selected >Select starting hour</option>');
        $("#start_time").append(data.data.freeHours.map(item => `<option value='${item}'>${item}</option>`));

        $("#end_time").html('<option value="" selected >-</option>');
        console.log(data.data.freeHours)
        console.log(data.data.takenIntervals)
        freeHours = data.data.freeHours;
        takenIntervals = data.data.takenIntervals;
    })
},
});


$("#start_time").on('change', (e) => {
    let validPeriod = [];
    let allIntervals = [];

    for(let i = parseInt(e.target.value) + 1; i <= 17; i++) {
        allIntervals.push({
            start_time: parseInt(e.target.value),
            end_time: i
        })
    }

    allIntervals.forEach(item => {
        let isTaken = false;
        takenIntervals.forEach(takenItem => {
            if(item.start_time < takenItem.end_time && item.end_time > takenItem.start_time) {
                isTaken = true
            }
        })
        
        !isTaken ? validPeriod.push(item.end_time) : "";
    });

    $("#end_time").html('<option value="" selected >Select ending hour</option>');
    $("#end_time").append(validPeriod.map(item => `<option value="${item}">${item}</option>`));
});

$("#submit").on('click', () => {
    var formData = new FormData();
    formData.append("name", $("#name").val())
    formData.append("number", $("#number").val())
    formData.append("email", $("#email").val())
    formData.append("service_id", $("#services").val() == "null" ? "" : $("#services").val())
    formData.append("status", $("#status").val() == "null" ? "" : $("#status").val())
    formData.append("medical_condition", $("#medical_condition").val())
    formData.append("special_requirements", $("#special_requirements").val())
    formData.append("day", selectedDate)
    formData.append("start_time", $("#start_time").val())
    formData.append("end_time", $("#end_time").val())

    fetch("http://localhost/pabau/api/apointments.php", {
        method: "POST",
        body: formData
    }).then(res => {
        return  res.json()
    }).then(data => {
        let all = [
        "name",
        "email",
        "number",
        "special_requirements",
        "medical_condition",
        "service_id",
        "status",
        "start_time",
        "end_time",
        "day"
    ];

        all.forEach(item => {
            $((`#${item}-error`)).html("");
        })

        console.log($("#services").val())
        if(data.message == "Validations have failed") {
            for (const key in data.data) {
                $((`#${key}-error`)).html(data.data[key]);
            }

            return;
        }

        location.reload();
    })

})

// 12 14 15

// 12 13 14 16