/**
 *	Publisher - Subscriper Design Pattern 
 *	===========
 */
 
 /**
  *	Class ArrayList
  *	===============
  */
function ArrayList()
{
	this.aList = []; //initialize with an empty array
}

/**
 *	Method: Count
 */
ArrayList.prototype.Count = function()
{
	return this.aList.length;
}

/**
 *	Method: Add
 */
ArrayList.prototype.Add = function( object )
{	
	return this.aList.push( object ); //Object are placed at the end of the array
}

/**
 *	Method: GetAt
 */
ArrayList.prototype.GetAt = function( index ) //Index must be a number
{
	if( index > -1 && index < this.aList.length )
		return this.aList[index];
	else
		return undefined; //Out of bound array, return undefined
}

/**
 *	Method: Clear
 */
ArrayList.prototype.Clear = function()
{
	this.aList = [];
}

/**
 *	Method: RemoveAt
 */
ArrayList.prototype.RemoveAt = function ( index ) // index must be a number
{
	var m_count = this.aList.length;
				
	if ( m_count > 0 && index > -1 && index < this.aList.length ) 
	{
		switch( index )
		{
			case 0:
				this.aList.shift();
				break;
			case m_count - 1:
				this.aList.pop();
				break;
			default:
				var head   = this.aList.slice( 0, index );
				var tail   = this.aList.slice( index + 1 );
				this.aList = head.concat( tail );
				break;
		}
	}
}

/**
 *	Method: Insert
 */
ArrayList.prototype.Insert = function ( object, index )
{
	var m_count       = this.aList.length;
	var m_returnValue = -1;
				
	if ( index > -1 && index <= m_count ) 
	{
		switch(index)
		{
			case 0:
				this.aList.unshift(object);
				m_returnValue = 0;
				break;
			case m_count:
				this.aList.push(object);
				m_returnValue = m_count;
				break;
			default:
				var head      = this.aList.slice(0, index - 1);
				var tail      = this.aList.slice(index);
				this.aList    = this.aList.concat(tail.unshift(object));
				m_returnValue = index;
				break;
		}
	}
				
	return m_returnValue;
}

/**
 *	Method: IndexOf
 */
ArrayList.prototype.IndexOf = function( object, startIndex )
{
	var m_count       = this.aList.length;
	var m_returnValue = - 1;
				
	if ( startIndex > -1 && startIndex < m_count ) 
	{
		var i = startIndex;
					
		while( i < m_count )
		{
			if ( this.aList[i] == object )
			{
				m_returnValue = i;
				break;
			}
						
			i++;
		}
	}
				
	return m_returnValue;
}
		

/**
 *	Method: LastIndexOf
 */
ArrayList.prototype.LastIndexOf = function( object, startIndex )
{
var m_count       = this.aList.length;
var m_returnValue = - 1;
			
if ( startIndex > -1 && startIndex < m_count ) 
{
	var i = m_count - 1;
				
	while( i >= startIndex )
	{
		if ( this.aList[i] == object )
		{
			m_returnValue = i;
			break;
		}
					
		i--;
	}
}
			
return m_returnValue;
}

/**
 *	Class Subject
 *	=============
 */
function Subject()
{
	this.observers = new ArrayList();
}

/**
 *	Mothod: notify: Meldung über Change an seine Beobachter
 */
Subject.prototype.Notify = function( context )
{
	var m_count = this.observers.Count();
			
	for( var i = 0; i < m_count; i++ )
		this.observers.GetAt(i).Update( context );
}

/**
 *	Add ein Beobachter
 */
Subject.prototype.AddObserver = function( observer )
{
	if( !observer.Update )
		throw 'Wrong parameter';

	this.observers.Add( observer );
}

/**
 *	remove ein Beobachter
 */
Subject.prototype.RemoveObserver = function( observer )
{
	if( !observer.Update )
		throw 'Wrong parameter';
	   
	this.observers.RemoveAt(this.observers.IndexOf( observer, 0 ));
}

/**
 *	Interface Observer
 *	Methode: Update (wird implementiert in konkreten Observer Class)
 *	==================
 */
function Observer()
{
	this.Update = function()
	{
		return;
	}
}

/**
 *	Vererbung function
 *  extension ist vererbrt vom base
 */
function inherits(base, extension)
{
	for (var property in base)
	{
		try
		{
			extension[property] = base[property];
		}
		catch(warning)
		{
		}
	}
}	

