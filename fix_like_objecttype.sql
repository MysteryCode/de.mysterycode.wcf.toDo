UPDATE wcf1_object_type SET objectType = 'de.mysterycode.wcf.toDo.toDo.like' WHERE objectType = 'de.mysterycode.wcf.toDo.toDo' AND definitionID = (SELECT def.definitionID FROM wcf1_object_type_definition def WHERE definitionName = 'com.woltlab.wcf.like.likeableObject');