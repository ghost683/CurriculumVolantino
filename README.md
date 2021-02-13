# Skills showcase Stefano Grado

The purpose of this repository is to offer an overview of my skills.

The project involves the creation of a simple REST call for the management of leaflets.

For the realization the indications given by the analysis reported below were followed.

It is to be considered my first approach with Framework cackePHP.

## Docs
...

  

## Stack

  

- Php 7

- Framework cackePHP 4

- Apache server

- Docker

  

## Usage

### Apache context:

 - download the zip from latest relases list and unzip.
 - configure apache virtualhost  to point to the folder app_volantini.
 - eventualy configure gzip compression for improve speed always in virtualhost config.
 - reload Apache.
 - try call API.

### Docker context:

TODO

  

### EndPoints valid links examples

 - /flyers.json?page=1&filter[is_published]=0
 - /flyers.json?page=2&limit=113 
 - /flyers.jsonpage=2&limit=50&fields=title,category&filter[category]=Discount&filter[is_published]=1


### EndPoint error links examples

 - invalid field in fields list
/flyers.json?page=1&filter[is_published]=0&fields=fooo
 - invalid filter name "foo"
/flyers.json?page=1&filter[is_published]=0&filter[foo]=bar
- invalid pagination, exided recordset
/flyers.json?page=2&limit=114
- invalid requested id, exided max id in recordset
/flyers/128.json

  

## assumptions
the following list indicates the appropriate assumptions considered during development

 - the flyer list is not necessarily sorted, to retrieve a flyer by given id loop the whole list.
 - the csv file has the header in the first line and is valid.
 - the recordset given to me was in .number format, for work in windows system i used an online converter. the resulted csv file was link above. the file presents a lot a invalid line, i considered they where correct but to be omitted.
 - fields name and filters name was compared in case sensitive way.
 - Unexpected or malformed parameters are not considered and not was considered errores.
 - the source file was stored in webroot/flyers_resources. the path is used as constant in FlyersUtils for example purpose only.




