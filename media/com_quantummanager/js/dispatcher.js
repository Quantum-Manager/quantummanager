//Загружать скрипт инлайном из джумлы в head

window.QuantumManagerLoadComplete = false;
window.QuantummanagerLists = [];

window.QuantumEvents = function () {

    this.listEvents = [];

    this.add = function (element, event, callback) {
        if (typeof element === 'string') {
            callback = event;
            event = element;
            element = null;
        }

        let eventObj = {};
        eventObj.n = event;
        eventObj.e = element;
        eventObj.c = callback;
        this.listEvents.push(eventObj);
    };


    this.trigger = function (event, filemanager, target) {
        let returns = [];
        for (let i=0;i<this.listEvents.length;i++) {
            if(this.listEvents[i].n === event) {
                returns.push(this.listEvents[i].c(filemanager, this.listEvents[i].e, target));
            }
        }
        return returns;
    };

};

window.QuantumEventsDispatcher = {
    listEvents: [],
    add: function (element, event, callback) {
        if (typeof element === 'string') {
            callback = event;
            event = element;
            element = null;
        }

        let eventObj = {};
        eventObj.n = event;
        eventObj.e = element;
        eventObj.c = callback;
        this.listEvents.push(eventObj);
    },
    build: function (fmLists) {
        for(let j=0;j<this.listEvents.length;j++) {
            for(let i=0;i<fmLists.length;i++) {
                fmLists[i].events.add(this.listEvents[j].e, this.listEvents[j].n, this.listEvents[j].c);
            }
        }
    },
    trigger: function (event) {
        for(let i=0;i<QuantummanagerLists.length;i++) {
            QuantummanagerLists[i].events.trigger(event, QuantummanagerLists[i]);
        }
    }

};