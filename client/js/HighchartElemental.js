document.addEventListener(
    "DOMContentLoaded",
    () => {
        if (typeof Highcharts === 'object') {
            //console.log('Highcharts exists');
            Highcharts.setOptions({
                colors: [
                    '#046b94', 
                    '#53ab57', 
                    '#1283b0', 
                    '#9a7a5b', 
                    '#67a7bf', 
                    '#9fba4d',
                    '#50a4a5', 
                    '#d09861', 
                    '#fcad30', 
                    '#f13d0c'
                ]
            });
        }
    }
);