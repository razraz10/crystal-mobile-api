# TODO List for Masha Project

## Features

-   [ ] Implement user authentication
-   [ ] Add CRUD functionality for posts/gets/put/delete - for inhbits,missions,markets tables.
-   [ ] For each table the frontend can search by sending selected_year & selected_month.
-   [] As for the permissions table has getAllPermissions
-   [] As for the users table has createUser / findUser (by string full_name || personal_number) / getAllUser
-   [ ] As for now the route of Post/Put/Delete onky user with permission_name = 'admin' can do so.
-   [ ] have l5-swager
-   [ ] As for now there is L5-swagger for all Controller per each function.
-   [ ] There is Enum of Color,CodePermission,Sttus,Month,EmployeeType.

## Remaining Tasks

-   [ ] Implement function to export the table as Excel - for each table (inhibts,missions,rekemAdvenced,markets)
-   [ ] Implement function of reports - need to export as report the all table (inhibts,missions, rekemAdvenced,markets)
-   [] Need to integrate with the frontend and find out if there is missing data I miss or mabe I send over data as I need to send.
-   [] I await for Gal or Ilay to find bags or somthing it not right on my code logic.
-   [ ] About DB::biginTransaction & DB::rollback ot DB::commit, on the service the DB::rollback not work. (I use that on the markets table -> each recoreds has one associated row on the months table)

## Bugs

## Improvements

-   [ ] I want to look and make sure the createUser function is right as for the createPermission
-   [ ] Need to make sure about the permissions I set on the permissions table is meets the requirement of the projects
-   [ ] Need to improve my L5-swagger. I await for comment form Gal.

## ERD table

-   [] markets table
-   has one to many realtion with months table - each row on the markets table has 12 months (with number that indicate on the month)
-   has one to many relation with the users table - by the fileds created_by & updated_by
-   [] missions table
-   has one to many relation with the users table - by the fileds created_by & updated_by
-   [] rekemadvanced table is the missions table it selef (but I Ignore the fileds of month,cumulative_per_month,plan_week_per_month)
-   -   has one to many relation with the users table - by the fileds created_by & updated_by
-   [] users table
-   has one to many relation with permissions table by the fileds permission_id
-   [] permissions table
-   has for now 3 rows permsission_code - 1 -> 'admin', 2 ->'edit', 3 -> 'client'

## Ideas

## Documentation
