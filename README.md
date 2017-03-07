<h1>Magento 2 module samples</h1>

<h2>Modules list</h2>

<h3>Backend module</h3>
* General backend classes
* Custom UI Listing column action
* Custom UI Form button (example of custom redirect after saving)
* Split Button example
* Js components. Form fields depended on Select field.

<h3>Category module</h3>
* Custom Category module (grid, form, CRUD)
* Need for extending by other modules
* Validation example

Pay attention: Category module don't know about CategoryTree module

<h3>CategoryCharacteristicGroup module</h3>
* Many to Many relation example (between Category and Characteristic Group)
* Controller plugin example for save processing
* Data provider plugin example for extending from from another module
* Relation object (with additional data) example
* Custom Search Criteria example (how to select entities based on relations)
* Full UI management of Many-to-Many relations (dynamic rows, grid, modal etc) in separate module

<h3>CategoryTree module</h3>
* Example of adding functionality to another module (in this case extends Category module)
* Add Ui Tree interface for Categories management
* Add Category Tree Structure (loading and manipulation)

<h3>CharacteristicGroup module</h3>
* Custom Characteristic Group module (grid, form, CRUD)
* Need for extending by other modules
* Validation example
* Many to Many relation example (between Characteristic Group and Characteristic) in one of related module

<h3>JsTree module</h3>
* Base jstree support
* Example of custom js widget (initializing and using)
* Alias for UI component in requirejs
* Backend theme customization (add custom css, add custom images, work with less)

<h3>Location module</h3>
* Module base configuration example
* One to Many relation example (Region <- Cities) in same module
* Data Per Store example
* Controllers example (massStatus, massDelete, inlineEdit, validate etc)
* CRUD operations example
* Repository example
* Work with Entity Manager
* UI elements configuration (grid, form)
* UI elements configuration for one to many relations management (dynamicRows, modal, insertListing)
* Data Provider example
* Web Api example
* Work with DI  (virtual types, preferences)
* Api tests, Integration tests
* Validation example (@spi extension point)

<h3>MagentoFix module</h3>
* Some fixes of Core code
* Example how to override core classes 

<h3>PerStoreDataSupport module</h3>
* Simple Data Per Store support
* Help module for managing data per store

<h2>Related Links</h2>
* <a href="http://devdocs.magento.com/">Magento 2 Dev Docs</a>
* <a href="https://github.com/magento/">Magento 2 github</a>
