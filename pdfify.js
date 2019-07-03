function waitFor(testFx, onReady, timeOutMillis) {
    var maxtimeOutMillis = timeOutMillis ? timeOutMillis : 3000, //< Default Max Timout is 3s
        start = new Date().getTime(),
        condition = false,
        interval = setInterval(function() {
            if ( (new Date().getTime() - start < maxtimeOutMillis) && !condition ) {
                // If not time-out yet and condition not yet fulfilled
                condition = (typeof(testFx) === "string" ? eval(testFx) : testFx()); //< defensive code
            } else {
                if(!condition) {
                    // If condition still not fulfilled (timeout but condition is 'false')
                    console.log("'waitFor()' timeout");
                    phantom.exit(1);
                } else {
                    // Condition fulfilled (timeout and/or condition is 'true')
                    console.log("'waitFor()' finished in " + (new Date().getTime() - start) + "ms.");
                    typeof(onReady) === "string" ? eval(onReady) : onReady(); //< Do what it's supposed to do once the condition is fulfilled
                    clearInterval(interval); //< Stop this interval
                }
            }
        }, 250); //< repeat check every 250ms
};

var page = require('webpage').create(),
    system = require('system'),
    address, output, size;

if (system.args.length < 3) {
    console.log('Usage: pdfify.js URL filename.pdf size dpi zoomFactor');
    console.log('  paper (pdf output) examples: "5in*7.5in", "10cm*20cm", "A4", "Letter"');
    phantom.exit(1);
} else {
    address = system.args[1];
    output = system.args[2];
    page.viewportSize = { width: 1000, height: 600 }; //this viewport forces bootstrap's medium grid pattern

    // Using "4A" as the inverse of "A4", for landscape
    if (system.args[3] == '4A') {
        page.paperSize = {
            width: '792px',
            height: '612px',
        };
    } else {
        page.paperSize = {
            width: '1200px',
            height: '1700px',
        };
    }
    page.settings.dpi = system.args[4];
    page.zoomFactor = system.args[5];

    page.open(address, function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit(1);
        } else {
            waitFor(function() {
                return page.evaluate(function() {
                    return ! $("#loading-bar")[0];
                });
            }, function() {
                page.render(output);
                phantom.exit();
            }, 10000);
        }
    });
}