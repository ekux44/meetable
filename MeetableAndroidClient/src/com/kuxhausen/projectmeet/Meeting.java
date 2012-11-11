package com.kuxhausen.projectmeet;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.Parcel;
import android.os.Parcelable;


public class Meeting implements Parcelable{

	
	int duration, startMinute, stopMinute, startHour, stopHour, startDay, stopDay, month, year;

	
	public JSONObject toJSON(){
		JSONObject object = new JSONObject();
		  try {
		    object.put("name", "Jack Hack");
		    object.put("length", duration);
		    object.put("start", 1352632469);
		    object.put("end", 1352635885);
		    object.put("narrowToOne", false);
		    
		    JSONArray jRay = new JSONArray();
		    JSONObject jUserDemo = new JSONObject();
		    jUserDemo.put("name", "Jared King");
		    jUserDemo.put("email", "");
		    jUserDemo.put("phone", "9186051721");
		    jRay.put(jUserDemo);
		    object.put("attendees", jRay );
		    
		    JSONObject jCreator = new JSONObject();
		    jCreator.put("name", "Eric");
		    jCreator.put("email", "erickuxhausen@gmail.com");
		    jCreator.put("phone", "");
		    object.put("creator", jCreator);
		    
		  } catch (JSONException e) {
		    e.printStackTrace();
		  }
		  
		return object;
	}


	@Override
	public int describeContents() {
		// TODO Auto-generated method stub
		return 0;
	}


	public Meeting () {
    }

	
	public static final Parcelable.Creator<Meeting> CREATOR
    = new Parcelable.Creator<Meeting>() {
	public Meeting createFromParcel(Parcel in) {
	    return new Meeting(in);
	}
	
	public Meeting[] newArray(int size) {
	    return new Meeting[size];
	}
	};
	
	public Meeting(Parcel in) {
	 
	 duration= in.readInt(); startMinute= in.readInt(); stopMinute= in.readInt(); startHour= in.readInt(); stopHour= in.readInt(); startDay= in.readInt(); stopDay= in.readInt(); month= in.readInt(); year= in.readInt();
	}

	@Override
	public void writeToParcel(Parcel out, int flags) {
		out.writeInt(duration); out.writeInt(startMinute); out.writeInt(stopMinute); out.writeInt(startHour); out.writeInt(stopHour); out.writeInt(startDay); out.writeInt(stopDay); out.writeInt(month); out.writeInt(year);
		
	}
	
	
}
