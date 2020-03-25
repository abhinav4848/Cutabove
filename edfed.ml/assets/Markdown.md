# cutabove
#### remove their "stg1w1_applied_for..." status from `cutabove_workshop` (member_id_1a...)
1. go to every stg1w1_applied_for, stg1w2_applied_for etc in the `cutabove` row, and set the corresponding workshop's member_id_#a to be 0 WHERE member_id_#a = `cutabove.clg_reg`

#### remove their "stg1w1..." (completed) status from `cutabove_workshop` (member_id_1...)
1. go to every stg1w1, stg1w2 etc in the cutabove row, and set the corresponding workshop's member_id_# to be 0 WHERE member_id_# = `cutabove.clg_reg`

---
 
# cutabove_council
#### remove their applied for entry at `cutabove_workshop` (supervisor_id_1..., supervisor_id_1a...)
1. find all rows in `cutabove_workshop_supervisors_applied_fo` where supervisor_id = `cutabove_council.council_id`
2. go to all workshops where `cutabove_workshop.workshop_id` = `cutabove_workshop_supervisors_applied_fo.workshop_id` and remove supervisor_id_# and supervisor_id_#a that match `cutabove_workshop_supervisors_applied_fo.supervisor_id`
#### remove their applied for/completed entry at `cutabove_workshop_supervisors_applied_fo`
1. got to the row where `cutabove_workshop_supervisors_applied_fo.supervisor_id` = `cutabove_council.council_id` and remove that row

---

# cutabove_workshop
#### remove its "member_id_1",  "member_id_1a" from `cutabove` (stg1w1_applied_for, stg1w1)
1. find the `cutabove.clg_reg` from each "member_id_#",  "member_id_#a" column and go to that `cutabove.clg_reg`
2. find the (`cutabove_workshop.level_name)_applied_for` and (`cutabove_workshop.level_name`) to 0 where the value in this column = `cutabove_workshop.workshop_id`
##### This is just a double safety check. 
1. row (`cutabove.clg_reg`) is found from member_id_# or member_id_#a, and column (`cutabove.stg#w#_applied_for` / `cutabove.stg#w#`) from `cutabove_workshop.level_name`
2. value is set to 0 only if pre-existing value was = `cutabove_workshop.workshop_id`

#### remove its "supervisor_id_1", "supervisor_id_1a" from `cutabove_workshop_supervisors_applied_fo` (supervisor_id,supervisor_attendance)
1. find the row in `cutabove_workshop_supervisors_applied_fo` where supervisor_id = "supervisor_id_1" or "supervisor_id_1a" and remove that row