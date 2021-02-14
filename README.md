# Skills showcase Stefano Grado

The purpose of this repository is to offer an overview of my skills.
The project involves the creation of a simple REST call for the management of leaflets.
For the realization the indications given by the analysis reported below were followed.
It is to be considered my first approach with Framework cackePHP.

## Docs

the doc, .number file and converted csv file are stored in repository root in folder Docs_and_source/
  

## Stack

- Php 7

- Framework cackePHP 4

- Apache server

- Docker

## Usage

### Apache context:

 - download the zip from latest relases and unzip.
 - run composer install
 - configure apache virtualhost  to point to the folder app_volantini.
 - eventualy configure gzip compression for improve speed always in virtualhost config.
 - reload Apache.
 - try call API.

### Docker context:

 - download the zip from latest release and unzip
 - go to the folder app_volantini
 - Run docker-compose build
 - Run docker-compose run
 - Service will listen on localhost:4000 

  

### EndPoints valid links examples

 - / will redirect to /flyers.json
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
 - the source file was stored in app_volantini/resource/flyers_resources/*. the path is defined in config/paths.php and used in FlyersUtils for example purpose only.

## Security consideration
 - for example purpose only, i diabled all kind off security token like csrf.
 - for enable access to a local simple frontend, i enable CORS origin from every domain (*).


## Extra

### Testing
test case are stored in app_volantini/tests/TestCase
to run test, run vendor\bin\phpunit from cli.

### swagger.yaml
fisrt version of swagger.yaml was upload in repository root. it's not a valid swagger file, i'm learning on.

### Simple frontend

for showcase only purpose i'm writing a very basic front end that consume REST api EP rendering a very basic bootstrap cards. it is not to be considered a REAL front end, it is not to be considered as the choice I would have made in a real context. the front end makes ajax calls to show results with simple bootstrap cards. for the purpose I have enabled the CORS in the backend without adding any type of control. everything is for demonstration purposes only. the front end expects the backend to listen on localhost:4000, so it works with the docker version presented.

#### Stack
 - Html
 - Css
 - Vanilla js
 - Bootstrap 4

#### Features
 - look for a flyer given its id
 - manage pagination results
 - NOT manage filtering

#### Usage
just open html file and test it. the backend must be listening on localhost: 4000.
searching for a id flyer will reset pagination to page 1.
