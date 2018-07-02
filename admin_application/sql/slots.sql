ALTER TABLE `kizzangslot`.`SlotTournament` 
CHANGE COLUMN `GameIDs` `GameIDs` SET('penguinriches','oakinthekitchen','happynewyear','holidayjoy','angrychefs','bankrollbandits','butterflytreasures','romancingriches','underseaworld2','crusadersquest','mummysrevenge','ghosttreasures') NOT NULL ;

Alter table kizzangslot.SlotGame add Disclaimer varchar(1000) after SecsTotal;