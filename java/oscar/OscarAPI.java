import java.util.*;
import uk.ac.cam.ch.wwmm.oscar.Oscar;
import uk.ac.cam.ch.wwmm.oscar.chemnamedict.entities.*;



public class OscarAPI
{
	public static void main(String[] args)
	{
		new OscarAPI(args);
		//System.out.println(args[0]);
	}

	public OscarAPI(String[] search)
	{
		Oscar oscar = new Oscar();
		for (String entry : search)
		{
			List<ResolvedNamedEntity> entities = oscar.findAndResolveNamedEntities(entry);
			for (ResolvedNamedEntity ne : entities)
			{
				System.out.println(ne.getSurface());	
			}
		}
	}
}
