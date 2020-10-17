# mySQL

CRUD operation with permission settings. Also it is mainly designed to work with Row number and Column Name.

Here Row number is not auto incremented ID. It reads/write data on row based on row index.
Also in order to identify each row uniquely the script needs to create 1st column 'id' to store unique row id on that so you need to specify 1st column as id. example 'id VARCHAR(15) NOT NULL' and then your rest columns.

- Current Version : 1.0.1 [beta]
- Created On : 17/10/2020
