# Elemental highcharts
This elemental block allows you to embed a Highchart or Highchart Stock chart into your elemental area.

## Installation
`composer require aetchell/elemental-highchart`

## Requirements
The default template uses [fontawesome v4](https://fontawesome.com/v4/) for chart control icons.

The follwing Silverstripe modules are also required in addition to the core framework:
- [dnadesign/silverstripe-elemental](https://github.com/silverstripe/silverstripe-elemental): ^4
- [silverstripe/display-logic](https://github.com/unclecheese/silverstripe-display-logic) ^2.0    

## Templates
You can override the default template by copying `/templates/aetchell/Highcharts/Elemental/ElementaHighchart.ss` to your own theme or the app folder. Be sure to maintain the directory structure due to the namespace: `/aetchell/Highcharts/Elemental/ElementaHighchart.ss`

## CSS
Sample styles can be found in:
`client/css/HighchartElemental.css`

## Highcharts notes
This implementation of Highcharts uses the CSV data source to add series to a chart. Information on using CSV data in Highcharts is available [here](https://api.highcharts.com/highcharts/data.csv).

You can find example CSV files in the [docs](./sample-data/) directory