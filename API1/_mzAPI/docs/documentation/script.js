(function () {
    function handler(event) {
        var el = event.target;
        if (el.matches('[filter-target]')) {
            var target = document.querySelector(el.getAttribute('filter-target'));
            if (target) filter(target, el.value);
        }
    }

    function filter(listEl, seachQuery) {
        clearTimeout(listEl.dataset.timeout);
        listEl.dataset.timeout = setTimeout(() => {
            var queryArr = (((seachQuery).toLowerCase()).split(" ")).filter((i) => i);

            listEl.querySelectorAll('[filter-value]').forEach((el) => {
                var string = el.getAttribute('filter-value').toLowerCase();
                var points = 0;
                for (var query of queryArr) {
                    if (string.indexOf(query) > -1) points++;
                    else points--;
                }
                if (points >= queryArr.length) el.classList.remove('hide');
                else el.classList.add('hide');
            });
        }, 500);
    }

    document.addEventListener('keyup', handler);
})();

function appendNav() {
    var output = document.getElementById("apis-output");
    var sample = document.getElementById("apis-sample");
    // output.innerHTML = sample.outerHTML;
    for (var key in Files) {
        var samp = sample.cloneNode(true);
        samp.classList.remove('hide');
        samp.removeAttribute('id');
        //
        samp.innerHTML = key;
        samp.dataset.api = key;
        samp.setAttribute('filter-value', key);
        //
        output.append(samp);
    }
    document.querySelectorAll("[data-api]").forEach(el => el.addEventListener('click', parseContent));
}
appendNav();

function appendHome() {

}

function parseContent(event) {
    var el = event.target;
    var key = el.dataset.api;
    //
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        return;
    }
    document.querySelectorAll("[data-api]").forEach(el => {
        el.classList.remove('active');
    });
    el.classList.add('active');

    //
    const ParamQuery = "mzParams::add";
    //
    var lines = (Files[key].split("\n")).filter((i) => i);
    var paramsArr = [];
    //
    for (var line of lines) {
        if (line.indexOf(ParamQuery) > -1) params(line);
    }
    //
    appendContent(paramsArr);
    //
    function params(line) {
        var s = line.indexOf(ParamQuery);
        line = line.slice(s + ParamQuery.length);
        var obj = {};
        //
        var FB = line.indexOf("(");
        obj['type'] = line.substring(0, FB);
        line = line.substring(FB + 1, line.length - 2).split(',');
        for (var i in line) {
            line[i] = line[i].replaceAll(/\"/g, '')
            line[i] = line[i].replaceAll(/\'/g, '')
            line[i] = line[i].toString();
            line[i] = line[i].trim();
        }
        obj['name'] = line[0];
        obj['required'] = false;
        //
        switch (obj['type']) {
            case "UPLOADFILE":
                if (line[1] == "true") obj['required'] = true;
                break;
            default:
                if (line[2] == "true") obj['required'] = true;
        }
        paramsArr.push(obj);
    }
}

function appendContent(paramsArr) {
    //
    params()
    //
    function params() {
        var output = document.getElementById("params-output");
        var sample = document.getElementById("params-sample");
        output.innerHTML = sample.outerHTML;
        for (var param of paramsArr) {
            var samp = sample.cloneNode(true);
            samp.classList.remove('hide');
            samp.removeAttribute('id');
            //
            samp.querySelectorAll('[data-id]').forEach(el => {
                switch (el.dataset.id) {
                    default:
                        el.innerHTML = param[el.dataset.id];
                }
            });;
            //
            output.append(samp);
        }
    }
}