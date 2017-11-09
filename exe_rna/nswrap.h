// Prototypes of functions located in NSWRAP.DLL.
// These functions are linked implicitly by the linker to the final code
// through the NSWRAP.LIB, which needs to be included in the project.
// The NSWRAP.DLL is just a wrapper around the NeuroShell Run-Time
// OLE Automation server NSRUN.DLL. The NSRUN.DLL must be properly
// installed and registered in the system registry before running this example.
// See NeuroShell Run-Time documentation for details.

long __stdcall OpenNetwork(char *fname, long *numinputs, long *numoutputs, float *enhgen);
long __stdcall FireNetwork(double *inarr, double *outarr, float *enhgen);
long __stdcall CloseNetwork(void);
double __stdcall GetMissingDataSpecifier(void);
