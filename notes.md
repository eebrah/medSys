#medical record keeping system

There are a whole lot of small climics and health care facilities in this country and they all have to keep records. How they do that though tends to be manual and though manageable with planning, is a bit inconvenient for other tasks such as querying and reports.

The goal of this piece of software is to enable the gradual digitisation of medical records to enable them to be looked up at the click of a button and for relevant information to be quickly and easily identified.

From initial rudimentary research [ one clinic ] It seems that a lot of the data is not structured and while not really a challenge in terms of creating a system to hold the data, it may proove to not be quite as usefull as intended when it comes to querying and reporting. Either way, we can start with what we have and see how it goes.

The access control parts of the application can be extended from previous applications.

Entry {
	$clerkID			// The clerk making the data entry
	$practitionerID		// The main doctor/nurse/clinical officer who made the observation, If others are involved they should be mentioned in the not
	$patientID

	$timestamp;			// When was this observation made, will default to the time that the entry is made
	
	$notes				// Textual description of observations, with a bit of practice, entry clerks will be able to structure the notes in such a way to convey structured data.

}

#Further information

There are certain measurements to be taken at every visit, whether the first, where the patient is registered or not. This measurements are weight, height and blood pressure.

I think it makes sense to introduce another data sctructure, the "visit" which will include this measurements an then any of a number of notes [ entries ]
