declare enum Stage {
    GROUP = 0,
    GROUPCODE = 1,
    GROUPNAME = 2,
    GROUPMEMBERS = 3
}
declare var currentstage: Stage;
declare var gamecode: any, groupcode: any, groupname: any;
declare function checkGamePin(): void;
declare function checkGroupCode(): void;
declare function nextStepSubmitGroupData(): void;
declare function submitGroupData(): void;
declare function nextStage(): void;
declare function getNewDiv(): Node;
