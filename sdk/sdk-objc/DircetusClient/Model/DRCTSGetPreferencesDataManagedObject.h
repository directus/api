#import <Foundation/Foundation.h>
#import <CoreData/CoreData.h>

/**
* directus.io
* API for directus.io
*
* OpenAPI spec version: 1.1
* 
*
* NOTE: This class is auto generated by the swagger code generator program.
* https://github.com/swagger-api/swagger-codegen.git
* Do not edit the class manually.
*/




NS_ASSUME_NONNULL_BEGIN

@interface DRCTSGetPreferencesDataManagedObject : NSManagedObject


@property (nullable, nonatomic, retain) NSNumber* _id;

@property (nullable, nonatomic, retain) NSNumber* user;

@property (nullable, nonatomic, retain) NSString* tableName;

@property (nullable, nonatomic, retain) NSString* title;

@property (nullable, nonatomic, retain) NSString* columnsVisible;

@property (nullable, nonatomic, retain) NSString* sort;

@property (nullable, nonatomic, retain) NSString* sortOrder;

@property (nullable, nonatomic, retain) NSString* status;

@property (nullable, nonatomic, retain) NSString* searchString;
@end

@interface DRCTSGetPreferencesDataManagedObject (GeneratedAccessors)

@end


NS_ASSUME_NONNULL_END
